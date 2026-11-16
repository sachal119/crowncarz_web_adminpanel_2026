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
     * Account identifier in config/webmail.php ('info' or 'accounts')
     */
    protected string $accountKey = 'info';

    /**
     * Named route prefix ('emails' or 'accounts_emails')
     */
    protected string $routePrefix = 'emails';

    /**
     * Get IMAP configuration for this mailbox account
     */
    protected function getImapConfig(): array
    {
        $accounts = config('webmail.accounts', []);
        if (isset($accounts[$this->accountKey]['imap'])) {
            return $accounts[$this->accountKey]['imap'];
        }
        return config('webmail.imap', []);
    }

    /**
     * Get SMTP configuration for this mailbox account
     */
    protected function getSmtpConfig(): array
    {
        $accounts = config('webmail.accounts', []);
        if (isset($accounts[$this->accountKey]['smtp'])) {
            return $accounts[$this->accountKey]['smtp'];
        }
        return config('webmail.smtp', []);
    }

    /**
     * Get mailer transport name for this mailbox account
     */
    protected function getMailer(): string
    {
        $accounts = config('webmail.accounts', []);
        if (isset($accounts[$this->accountKey]['mailer'])) {
            return $accounts[$this->accountKey]['mailer'];
        }
        return 'hostinger_webmail';
    }

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

        $imapConfig = $this->getImapConfig();
        $mailboxUsername = $imapConfig['username'] ?? ($this->accountKey === 'accounts' ? 'accounts@crowncarz.com' : 'info@crowncarz.com');
        $mailboxTitle = ($this->accountKey === 'accounts') ? 'Accounts Webmail' : 'Business Webmail';

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
            $imap = new HostingerImapClient($imapConfig);
            $folders = $imap->getFolders($forceRefresh);
            $messagesData = $imap->getMessages($folder, $page, 25, $search);
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();
            Log::error("Hostinger Webmail Error ({$this->accountKey}): " . $errorMessage);
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
            'mailboxUsername' => $mailboxUsername,
            'mailboxTitle' => $mailboxTitle,
            'routePrefix' => $this->routePrefix,
            'accountKey' => $this->accountKey,
        ]);
    }

    /**
     * Query-string based single message fetcher (bulletproof against server route rewrites)
     */
    public function showQuery(Request $request)
    {
        $id = (int) $request->query('id', 0);
        return $this->show($request, $id);
    }

    /**
     * Fetch single message details via AJAX or view
     */
    public function show(Request $request, int $id)
    {
        if (!session('admin_logged_in')) {
            return response()->json(['success' => false, 'error' => 'Unauthorized session. Please login.'], 401);
        }

        $folder = $request->query('folder', 'INBOX');
        if (empty($folder) || $folder === 'message') {
            $folder = 'INBOX';
        }
        $folder = urldecode((string) $folder);
        if (base64_encode(base64_decode($folder, true) ?: '') === $folder) {
            $decoded = base64_decode($folder, true);
            if ($decoded !== false)
                $folder = $decoded;
        }

        try {
            $imap = new HostingerImapClient($this->getImapConfig());
            $message = $imap->getMessage($folder, $id, true);

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        } catch (\Throwable $e) {
            Log::error("Failed to load email #{$id} from folder '{$folder}' ({$this->accountKey}): " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 200);
        }
    }

    /**
     * Legacy route forwarder
     */
    public function showLegacy(Request $request, string $folder, int $id)
    {
        if ($folder === 'message') {
            $folder = $request->query('folder', 'INBOX');
        }
        $request->merge(['folder' => $folder]);
        return $this->show($request, $id);
    }

    /**
     * Download or view an attachment
     */
    public function downloadAttachment(Request $request, int $id, int $index)
    {
        if (!session('admin_logged_in')) {
            abort(403, 'Unauthorized');
        }

        $folder = $request->query('folder', 'INBOX');
        $folder = urldecode((string) $folder);
        if (base64_encode(base64_decode($folder, true) ?: '') === $folder) {
            $decoded = base64_decode($folder, true);
            if ($decoded !== false)
                $folder = $decoded;
        }
        if (base64_encode(base64_decode($folder, true) ?: '') === $folder) {
            $decoded = base64_decode($folder, true);
            if ($decoded !== false)
                $folder = $decoded;
        }

        try {
            $imap = new HostingerImapClient($this->getImapConfig());
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
            $smtpConfig = $this->getSmtpConfig();
            $imapConfig = $this->getImapConfig();
            $fromAddress = $smtpConfig['from_address'] ?: ($smtpConfig['username'] ?? $imapConfig['username']);
            $fromName = $smtpConfig['from_name'] ?: 'Crown Carz';
            $mailer = $this->getMailer();

            // Parse recipient lists
            $toEmails = array_filter(array_map('trim', explode(',', $validated['to'])));
            $ccEmails = !empty($validated['cc']) ? array_filter(array_map('trim', explode(',', $validated['cc']))) : [];
            $bccEmails = !empty($validated['bcc']) ? array_filter(array_map('trim', explode(',', $validated['bcc']))) : [];

            // Convert plain text body newlines into clean HTML
            $htmlBody = nl2br(e($validated['body']));

            // Send via dedicated Hostinger Webmail SMTP mailer
            $sentMessage = Mail::mailer($mailer)->html($htmlBody, function ($message) use ($fromAddress, $fromName, $validated, $toEmails, $ccEmails, $bccEmails, $request) {
                $message->from($fromAddress, $fromName)
                    ->subject($validated['subject']);

                foreach ($toEmails as $to) {
                    if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
                        $message->to($to);
                    }
                }
                foreach ($ccEmails as $cc) {
                    if (filter_var($cc, FILTER_VALIDATE_EMAIL)) {
                        $message->cc($cc);
                    }
                }
                foreach ($bccEmails as $bcc) {
                    if (filter_var($bcc, FILTER_VALIDATE_EMAIL)) {
                        $message->bcc($bcc);
                    }
                }

                // Headers for threading (Reply / Forward)
                if (!empty($validated['in_reply_to'])) {
                    $message->getHeaders()->addTextHeader('In-Reply-To', $validated['in_reply_to']);
                }
                if (!empty($validated['references'])) {
                    $message->getHeaders()->addTextHeader('References', $validated['references']);
                }

                // Handle uploaded file attachments
                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        if ($file->isValid()) {
                            $message->attach($file->getRealPath(), [
                                'as' => $file->getClientOriginalName(),
                                'mime' => $file->getMimeType(),
                            ]);
                        }
                    }
                }
            });

            // Synchronize with Hostinger IMAP Sent Folder
            if ($sentMessage) {
                try {
                    $rawMime = $sentMessage->toString();
                    $imap = new HostingerImapClient($imapConfig);
                    $imap->appendToSent($rawMime);
                    Cache::forget('hostinger_webmail_folders_' . md5($imapConfig['username'] ?? 'info@crowncarz.com'));
                } catch (\Throwable $imapEx) {
                    Log::warning('Sent mail IMAP sync notice: ' . $imapEx->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Email sent successfully via Hostinger!',
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to send webmail (' . $this->accountKey . '): ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to send email: ' . $e->getMessage(),
            ], 200);
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
            $imap = new HostingerImapClient($this->getImapConfig());
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
            $imap = new HostingerImapClient($this->getImapConfig());
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
            $imap = new HostingerImapClient($this->getImapConfig());
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
