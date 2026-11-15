<?php

namespace App\Http\Controllers;

use App\Services\HostingerImapClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class WebmailController extends Controller
{
    /**
     * Render the Webmail Dashboard interface
     */
    public function index(Request $request)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('login')->with('error', 'Please login to access webmail.');
        }

        $folder = $request->query('folder', 'INBOX');
        // Decode if base64 passed
        if (base64_encode(base64_decode($folder, true) ?: '') === $folder) {
            $decoded = base64_decode($folder, true);
            if ($decoded !== false) {
                $folder = $decoded;
            }
        }

        $page = max(1, (int) $request->query('page', 1));
        $search = $request->query('q');
        $forceRefresh = $request->boolean('refresh');

        $errorMessage = null;
        $folders = [];
        $messagesData = [
            'messages' => [],
            'total' => 0,
            'page' => $page,
            'per_page' => 25,
            'total_pages' => 1,
            'folder' => $folder,
        ];

        try {
            $imap = new HostingerImapClient();
            $folders = $imap->getFolders($forceRefresh);
            $messagesData = $imap->getMessages($folder, $page, 25, $search);
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();
            Log::error('Hostinger Webmail Error: ' . $errorMessage);
        }

        return view('emails.webmail.index', [
            'folders' => $folders,
            'messages' => $messagesData['messages'],
            'total' => $messagesData['total'],
            'currentPage' => $messagesData['page'],
            'totalPages' => $messagesData['total_pages'],
            'currentFolder' => $folder,
            'searchQuery' => $search,
            'errorMessage' => $errorMessage,
            'mailboxUsername' => config('webmail.imap.username', 'info@crowncarz.com'),
        ]);
    }

    /**
     * Fetch single message details via AJAX or view
     */
    public function show(Request $request, string $folder, int $id)
    {
        if (!session('admin_logged_in')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $folder = urldecode($folder);
        if (base64_encode(base64_decode($folder, true) ?: '') === $folder) {
            $decoded = base64_decode($folder, true);
            if ($decoded !== false) $folder = $decoded;
        }

        try {
            $imap = new HostingerImapClient();
            $message = $imap->getMessage($folder, $id, true);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                ]);
            }

            return view('emails.webmail.show', compact('message', 'folder'));
        } catch (\Throwable $e) {
            Log::error("Failed to load email #{$id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download or view an attachment
     */
    public function downloadAttachment(Request $request, string $folder, int $id, int $index)
    {
        if (!session('admin_logged_in')) {
            abort(403, 'Unauthorized');
        }

        $folder = urldecode($folder);
        if (base64_encode(base64_decode($folder, true) ?: '') === $folder) {
            $decoded = base64_decode($folder, true);
            if ($decoded !== false) $folder = $decoded;
        }

        try {
            $imap = new HostingerImapClient();
            $msg = $imap->getMessage($folder, $id, false);

            $attachment = null;
            foreach ($msg['attachments'] as $att) {
                if ($att['index'] === $index) {
                    $attachment = $att;
                    break;
                }
            }

            if (!$attachment) {
                abort(404, 'Attachment not found');
            }

            $binaryData = base64_decode($attachment['data']);
            $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $attachment['filename'] ?: 'attachment');

            return response($binaryData, 200, [
                'Content-Type' => $attachment['content_type'] ?: 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Length' => strlen($binaryData),
            ]);
        } catch (\Throwable $e) {
            abort(500, 'Error downloading attachment: ' . $e->getMessage());
        }
    }

    /**
     * Send email via Hostinger authenticated SMTP
     */
    public function send(Request $request)
    {
        if (!session('admin_logged_in')) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'to' => 'required|string',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'cc' => 'nullable|string',
            'bcc' => 'nullable|string',
            'in_reply_to' => 'nullable|string',
            'references' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:20480', // 20MB limit per file
        ]);

        try {
            $fromAddress = config('webmail.smtp.from_address', 'info@crowncarz.com');
            $fromName = config('webmail.smtp.from_name', 'Crown Carz');

            // Parse recipient lists
            $toEmails = array_map('trim', explode(',', $validated['to']));
            $ccEmails = !empty($validated['cc']) ? array_map('trim', explode(',', $validated['cc'])) : [];
            $bccEmails = !empty($validated['bcc']) ? array_map('trim', explode(',', $validated['bcc'])) : [];

            // Build Symfony Email object
            $email = (new Email())
                ->from(new Address($fromAddress, $fromName))
                ->subject($validated['subject'])
                ->html($validated['body']);

            foreach ($toEmails as $to) {
                if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
                    $email->addTo($to);
                }
            }
            foreach ($ccEmails as $cc) {
                if (filter_var($cc, FILTER_VALIDATE_EMAIL)) {
                    $email->addCc($cc);
                }
            }
            foreach ($bccEmails as $bcc) {
                if (filter_var($bcc, FILTER_VALIDATE_EMAIL)) {
                    $email->addBcc($bcc);
                }
            }

            // Headers for threading (Reply / Forward)
            if (!empty($validated['in_reply_to'])) {
                $email->getHeaders()->addTextHeader('In-Reply-To', $validated['in_reply_to']);
            }
            if (!empty($validated['references'])) {
                $email->getHeaders()->addTextHeader('References', $validated['references']);
            }

            // Handle uploaded file attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    if ($file->isValid()) {
                        $email->attachFromPath(
                            $file->getRealPath(),
                            $file->getClientOriginalName(),
                            $file->getMimeType()
                        );
                    }
                }
            }

            // Send via dedicated Hostinger Webmail SMTP mailer
            Mail::mailer('hostinger_webmail')->send(new \Illuminate\Mail\Mailable(), [], function ($message) use ($email) {
                // Transfer Symfony Email settings to Swift/Symfony Message
                $symfonyMessage = $message->getSymfonyMessage();
                if ($symfonyMessage instanceof \Symfony\Component\Mime\Email) {
                    $symfonyMessage->setHeaders($email->getHeaders());
                    $symfonyMessage->setBody($email->getBody());
                }
            });

            // Synchronize with Hostinger IMAP Sent Folder
            try {
                $imap = new HostingerImapClient();
                $imap->appendToSent($email->toString());
                Cache::forget('hostinger_webmail_folders_' . md5(config('webmail.imap.username', 'info@crowncarz.com')));
            } catch (\Throwable $imapEx) {
                Log::warning('Sent mail IMAP sync notice: ' . $imapEx->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Email sent successfully via Hostinger!',
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to send webmail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to send email: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark message as Read / Unread
     */
    public function markRead(Request $request)
    {
        if (!session('admin_logged_in')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $id = (int) $request->input('id');
        $folder = $request->input('folder', 'INBOX');
        $read = $request->boolean('read', true);

        try {
            $imap = new HostingerImapClient();
            $imap->markAsRead($folder, $id, $read);

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Toggle Star / Flagged
     */
    public function toggleStar(Request $request)
    {
        if (!session('admin_logged_in')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $id = (int) $request->input('id');
        $folder = $request->input('folder', 'INBOX');
        $starred = $request->boolean('starred', true);

        try {
            $imap = new HostingerImapClient();
            $imap->toggleStar($folder, $id, $starred);

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete message (Moves to Trash or expunges)
     */
    public function destroy(Request $request)
    {
        if (!session('admin_logged_in')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $id = (int) $request->input('id');
        $folder = $request->input('folder', 'INBOX');

        try {
            $imap = new HostingerImapClient();
            $imap->deleteMessage($folder, $id);

            return response()->json([
                'success' => true,
                'message' => 'Message deleted successfully',
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
