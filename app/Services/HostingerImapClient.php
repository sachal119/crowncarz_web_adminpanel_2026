<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class HostingerImapClient
{
    protected string $host;
    protected int $port;
    protected string $encryption;
    protected string $username;
    protected string $password;
    protected int $timeout;
    protected bool $validateCert;

    /** @var resource|null */
    protected $stream = null;
    protected int $tagCounter = 0;
    protected ?string $selectedFolder = null;

    public function __construct(?array $config = null)
    {
        $cfg = $config ?? config('webmail.imap', []);

        $this->host = (string) ($cfg['host'] ?? 'imap.hostinger.com');
        $this->port = (int) ($cfg['port'] ?? 993);
        $this->encryption = strtolower((string) ($cfg['encryption'] ?? 'ssl'));
        $this->username = (string) ($cfg['username'] ?? 'info@crowncarz.com');
        $this->password = (string) ($cfg['password'] ?? '');
        $this->timeout = (int) ($cfg['timeout'] ?? 30);
        $this->validateCert = (bool) ($cfg['validate_cert'] ?? false);
    }

    /**
     * Connect to Hostinger IMAP server over SSL/TLS
     */
    public function connect(): bool
    {
        if ($this->stream && is_resource($this->stream)) {
            return true;
        }

        if (empty($this->username) || empty($this->password)) {
            $envKey = str_starts_with($this->username, 'accounts') ? 'HOSTINGER_ACCOUNTS_EMAIL_PASSWORD' : 'HOSTINGER_WEBMAIL_IMAP_PASSWORD';
            throw new Exception("Hostinger Webmail credentials for {$this->username} are missing. Please configure {$envKey} in your .env file.");
        }

        $protocol = ($this->encryption === 'ssl' || $this->port === 993) ? 'ssl://' : '';
        $target = $protocol . $this->host . ':' . $this->port;

        $contextOptions = [
            'ssl' => [
                'verify_peer' => $this->validateCert,
                'verify_peer_name' => $this->validateCert,
                'allow_self_signed' => !$this->validateCert,
            ]
        ];
        $context = stream_context_create($contextOptions);

        $errno = 0;
        $errstr = '';
        $this->stream = @stream_socket_client(
            $target,
            $errno,
            $errstr,
            $this->timeout,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (!$this->stream || !is_resource($this->stream)) {
            throw new Exception("Unable to connect to Hostinger IMAP server ({$this->host}:{$this->port}): [{$errno}] {$errstr}");
        }

        stream_set_timeout($this->stream, $this->timeout);

        // Read server banner
        $greeting = $this->readLine();
        if (!str_starts_with($greeting, '* OK')) {
            $this->disconnect();
            throw new Exception("Unexpected greeting from Hostinger IMAP server: {$greeting}");
        }

        // Authenticate
        $res = $this->sendCommand('LOGIN ' . $this->escape($this->username) . ' ' . $this->escape($this->password));
        if ($res['status'] !== 'OK') {
            $this->disconnect();
            throw new Exception("Hostinger IMAP login failed for '{$this->username}': " . ($res['message'] ?? 'Authentication rejected'));
        }

        return true;
    }

    /**
     * Close connection
     */
    public function disconnect(): void
    {
        if ($this->stream && is_resource($this->stream)) {
            try {
                $this->sendCommand('LOGOUT');
            } catch (\Throwable $e) {}
            @fclose($this->stream);
        }
        $this->stream = null;
        $this->selectedFolder = null;
    }

    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * Discover all folders with unread and total counts
     */
    public function getFolders(bool $forceRefresh = false): array
    {
        $cacheKey = 'hostinger_webmail_folders_' . md5($this->username);
        if (!$forceRefresh && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $this->connect();
        $response = $this->sendCommand('LIST "" "*"');

        $folders = [];
        foreach ($response['lines'] as $line) {
            // Pattern: * LIST (\HasNoChildren) "/" "INBOX" or * LIST (\HasNoChildren) "/" INBOX
            if (preg_match('/^\*\s+LIST\s+\((.*?)\)\s+("[^"]*"|\S+)\s+(.+)$/i', trim($line), $matches)) {
                $flags = explode(' ', trim($matches[1]));
                $rawName = trim($matches[3]);
                $name = trim($rawName, '"');

                if (in_array('\\Noselect', $flags)) {
                    continue;
                }

                $meta = $this->mapFolderMetadata($name);

                // Fetch message & unseen count
                $statusRes = $this->sendCommand('STATUS ' . $this->escape($name) . ' (MESSAGES UNSEEN)');
                $messages = 0;
                $unseen = 0;
                if (!empty($statusRes['lines'])) {
                    foreach ($statusRes['lines'] as $sLine) {
                        if (preg_match('/MESSAGES\s+(\d+)/i', $sLine, $mMatch)) {
                            $messages = (int) $mMatch[1];
                        }
                        if (preg_match('/UNSEEN\s+(\d+)/i', $sLine, $uMatch)) {
                            $unseen = (int) $uMatch[1];
                        }
                    }
                }

                $folders[] = [
                    'id' => base64_encode($name),
                    'name' => $name,
                    'display_name' => $meta['display_name'],
                    'icon' => $meta['icon'],
                    'type' => $meta['type'],
                    'messages' => $messages,
                    'unseen' => $unseen,
                ];
            }
        }

        // Fallback default folders if list returned empty
        if (empty($folders)) {
            $folders = [
                ['id' => base64_encode('INBOX'), 'name' => 'INBOX', 'display_name' => 'Inbox', 'icon' => 'bi-inbox-fill', 'type' => 'inbox', 'messages' => 0, 'unseen' => 0],
                ['id' => base64_encode('Sent'), 'name' => 'Sent', 'display_name' => 'Sent', 'icon' => 'bi-send-fill', 'type' => 'sent', 'messages' => 0, 'unseen' => 0],
                ['id' => base64_encode('Drafts'), 'name' => 'Drafts', 'display_name' => 'Drafts', 'icon' => 'bi-file-earmark-text-fill', 'type' => 'drafts', 'messages' => 0, 'unseen' => 0],
                ['id' => base64_encode('Trash'), 'name' => 'Trash', 'display_name' => 'Trash', 'icon' => 'bi-trash-fill', 'type' => 'trash', 'messages' => 0, 'unseen' => 0],
                ['id' => base64_encode('Junk'), 'name' => 'Junk', 'display_name' => 'Spam / Junk', 'icon' => 'bi-exclamation-octagon-fill', 'type' => 'spam', 'messages' => 0, 'unseen' => 0],
            ];
        }

        // Sort: Inbox first, then Sent, Drafts, Trash, Spam, Others
        usort($folders, function ($a, $b) {
            $order = ['inbox' => 1, 'sent' => 2, 'drafts' => 3, 'trash' => 4, 'spam' => 5, 'other' => 6];
            $oA = $order[$a['type']] ?? 10;
            $oB = $order[$b['type']] ?? 10;
            return $oA <=> $oB;
        });

        Cache::put($cacheKey, $folders, 45);
        return $folders;
    }

    /**
     * Select a mailbox folder
     */
    public function selectFolder(string $folder = 'INBOX'): array
    {
        $this->connect();
        if ($this->selectedFolder === $folder) {
            return ['status' => 'OK'];
        }

        $res = $this->sendCommand('SELECT ' . $this->escape($folder));
        if ($res['status'] !== 'OK') {
            throw new Exception("Unable to open folder '{$folder}': " . ($res['message'] ?? 'Folder not found'));
        }

        $this->selectedFolder = $folder;
        return $res;
    }

    /**
     * Get paginated messages in a folder with optional search query
     */
    public function getMessages(string $folder = 'INBOX', int $page = 1, int $perPage = 25, ?string $search = null): array
    {
        $this->selectFolder($folder);

        if (!empty($search)) {
            $search = trim($search);
            // Search text across subjects, from, and bodies
            $searchCommand = 'SEARCH TEXT ' . $this->escape($search);
        } else {
            $searchCommand = 'SEARCH ALL';
        }

        $searchRes = $this->sendCommand($searchCommand);
        $messageIds = [];
        foreach ($searchRes['lines'] as $line) {
            if (str_starts_with($line, '* SEARCH')) {
                $parts = explode(' ', trim(substr($line, 8)));
                foreach ($parts as $p) {
                    if (is_numeric($p) && (int)$p > 0) {
                        $messageIds[] = (int) $p;
                    }
                }
            }
        }

        $total = count($messageIds);
        // Newest messages first
        $messageIds = array_reverse($messageIds);

        $offset = ($page - 1) * $perPage;
        $pageIds = array_slice($messageIds, $offset, $perPage);

        if (empty($pageIds)) {
            return [
                'messages' => [],
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => max(1, (int) ceil($total / $perPage)),
                'folder' => $folder,
            ];
        }

        // Fetch headers for page items
        $idSequence = implode(',', $pageIds);
        $fetchRes = $this->sendCommand("FETCH {$idSequence} (FLAGS INTERNALDATE RFC822.SIZE BODY.PEEK[HEADER.FIELDS (SUBJECT FROM TO DATE MESSAGE-ID IN-REPLY-TO REFERENCES)])");

        $parsedMessages = [];
        $currentMsg = null;
        $inHeader = false;
        $headerBuffer = '';

        foreach ($fetchRes['lines'] as $line) {
            if (preg_match('/^\*\s+(\d+)\s+FETCH\s+\((.*)$/i', $line, $fMatch)) {
                $msgId = (int) $fMatch[1];
                $metaStr = $fMatch[2];

                $isSeen = str_contains($metaStr, '\\Seen');
                $isFlagged = str_contains($metaStr, '\\Flagged');
                $isAnswered = str_contains($metaStr, '\\Answered');

                $currentMsg = [
                    'id' => $msgId,
                    'is_seen' => $isSeen,
                    'is_starred' => $isFlagged,
                    'is_answered' => $isAnswered,
                    'subject' => '(No Subject)',
                    'from_name' => '',
                    'from_email' => '',
                    'to' => '',
                    'date' => '',
                    'date_formatted' => '',
                    'size' => 0,
                    'has_attachments' => false,
                ];

                if (preg_match('/RFC822\.SIZE\s+(\d+)/i', $metaStr, $szMatch)) {
                    $currentMsg['size'] = (int) $szMatch[1];
                }

                $inHeader = true;
                $headerBuffer = '';
                continue;
            }

            if ($inHeader) {
                if (str_starts_with($line, ')') || preg_match('/^\*\s+\d+\s+FETCH/i', $line)) {
                    $inHeader = false;
                    if ($currentMsg) {
                        $this->parseHeaderBuffer($headerBuffer, $currentMsg);
                        $parsedMessages[$currentMsg['id']] = $currentMsg;
                    }
                    $currentMsg = null;
                } else {
                    $headerBuffer .= $line . "\n";
                }
            }
        }

        if ($currentMsg && $inHeader) {
            $this->parseHeaderBuffer($headerBuffer, $currentMsg);
            $parsedMessages[$currentMsg['id']] = $currentMsg;
        }

        // Order according to original pageIds sequence
        $finalList = [];
        foreach ($pageIds as $id) {
            if (isset($parsedMessages[$id])) {
                $finalList[] = $parsedMessages[$id];
            }
        }

        return [
            'messages' => $finalList,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => max(1, (int) ceil($total / $perPage)),
            'folder' => $folder,
        ];
    }

    /**
     * Get single email with full HTML body, plain text, and attachments
     */
    public function getMessage(string $folder, int $id, bool $autoMarkSeen = true): array
    {
        $this->selectFolder($folder);

        // Fetch full raw message
        $fetchRes = $this->sendCommand("FETCH {$id} (FLAGS INTERNALDATE RFC822.SIZE BODY.PEEK[])");
        if (($fetchRes['status'] ?? '') !== 'OK') {
            throw new Exception("IMAP Fetch failed for message #{$id}: " . ($fetchRes['message'] ?? 'Unknown error'));
        }

        $rawMessage = '';
        $lines = $fetchRes['lines'] ?? [];

        // 1. Check if literal was captured in response
        for ($i = 0; $i < count($lines); $i++) {
            $line = $lines[$i];
            if (preg_match('/\{(\d+)\}$/', $line)) {
                if (isset($lines[$i + 1]) && strlen($lines[$i + 1]) > 0) {
                    $rawMessage = $lines[$i + 1];
                    break;
                }
            }
        }

        // 2. Fallback: try standard RFC822 fetch if BODY.PEEK was empty
        if (empty($rawMessage)) {
            $rfcRes = $this->sendCommand("FETCH {$id} (RFC822)");
            $rfcLines = $rfcRes['lines'] ?? [];
            for ($j = 0; $j < count($rfcLines); $j++) {
                if (preg_match('/\{(\d+)\}$/', $rfcLines[$j])) {
                    if (isset($rfcLines[$j + 1]) && strlen($rfcLines[$j + 1]) > 0) {
                        $rawMessage = $rfcLines[$j + 1];
                        break;
                    }
                }
            }
        }

        // 3. Fallback: stream lines if not delivered as single literal
        if (empty($rawMessage)) {
            $found = false;
            foreach ($lines as $line) {
                if (preg_match('/^\*\s+\d+\s+FETCH/i', $line)) {
                    $found = true;
                    continue;
                }
                if ($found) {
                    if (str_starts_with($line, 'TAG') || $line === ')') {
                        break;
                    }
                    $rawMessage .= $line . "\r\n";
                }
            }
        }

        if (empty($rawMessage)) {
            throw new Exception("Message #{$id} content could not be read from folder '{$folder}'.");
        }

        if ($autoMarkSeen) {
            $this->markAsRead($folder, $id, true);
        }

        return $this->parseFullMimeMessage($id, $rawMessage);
    }

    /**
     * Mark message as Seen or Unseen
     */
    public function markAsRead(string $folder, int $id, bool $read = true): bool
    {
        $this->selectFolder($folder);
        $action = $read ? '+FLAGS (\\Seen)' : '-FLAGS (\\Seen)';
        $res = $this->sendCommand("STORE {$id} {$action}");
        return $res['status'] === 'OK';
    }

    /**
     * Toggle Star / Flagged
     */
    public function toggleStar(string $folder, int $id, bool $starred): bool
    {
        $this->selectFolder($folder);
        $action = $starred ? '+FLAGS (\\Flagged)' : '-FLAGS (\\Flagged)';
        $res = $this->sendCommand("STORE {$id} {$action}");
        return $res['status'] === 'OK';
    }

    /**
     * Delete message (Moves to Trash, or expunges if already in Trash)
     */
    public function deleteMessage(string $folder, int $id): bool
    {
        $this->selectFolder($folder);
        $isTrash = in_array(strtolower($folder), ['trash', 'deleted messages', 'bin', 'deleted']);

        if (!$isTrash) {
            // Find Trash folder name
            $folders = $this->getFolders();
            $trashFolder = 'Trash';
            foreach ($folders as $f) {
                if ($f['type'] === 'trash') {
                    $trashFolder = $f['name'];
                    break;
                }
            }

            // Copy to Trash
            $this->sendCommand("COPY {$id} " . $this->escape($trashFolder));
        }

        // Mark as Deleted and Expunge
        $this->sendCommand("STORE {$id} +FLAGS (\\Deleted)");
        $this->sendCommand('EXPUNGE');

        // Invalidate folder counts
        Cache::forget('hostinger_webmail_folders_' . md5($this->username));
        return true;
    }

    /**
     * Append sent raw MIME email into Sent folder
     */
    public function appendToSent(string $rawMime): bool
    {
        try {
            $folders = $this->getFolders();
            $sentFolder = 'Sent';
            foreach ($folders as $f) {
                if ($f['type'] === 'sent') {
                    $sentFolder = $f['name'];
                    break;
                }
            }

            $length = strlen($rawMime);
            $this->connect();
            $res = $this->sendCommand("APPEND " . $this->escape($sentFolder) . " (\\Seen) {" . $length . "}");
            if (str_starts_with($res['raw_first'] ?? '', '+')) {
                // Send raw data
                fwrite($this->stream, $rawMime . "\r\n");
                $finalLine = $this->readLine();
                return str_contains($finalLine, 'OK');
            }
            return false;
        } catch (\Throwable $e) {
            Log::warning('IMAP append to sent skipped: ' . $e->getMessage());
            return false;
        }
    }

    // =========================================================================
    // 🛠️ INTERNAL PARSING & HELPER METHODS
    // =========================================================================

    protected function mapFolderMetadata(string $name): array
    {
        $lower = strtolower($name);
        if ($lower === 'inbox') {
            return ['display_name' => 'Inbox', 'icon' => 'bi-inbox-fill', 'type' => 'inbox'];
        }
        if (str_contains($lower, 'sent')) {
            return ['display_name' => 'Sent', 'icon' => 'bi-send-fill', 'type' => 'sent'];
        }
        if (str_contains($lower, 'draft')) {
            return ['display_name' => 'Drafts', 'icon' => 'bi-file-earmark-text-fill', 'type' => 'drafts'];
        }
        if (str_contains($lower, 'trash') || str_contains($lower, 'bin') || str_contains($lower, 'deleted')) {
            return ['display_name' => 'Trash', 'icon' => 'bi-trash-fill', 'type' => 'trash'];
        }
        if (str_contains($lower, 'junk') || str_contains($lower, 'spam')) {
            return ['display_name' => 'Spam / Junk', 'icon' => 'bi-exclamation-octagon-fill', 'type' => 'spam'];
        }
        return ['display_name' => ucfirst($name), 'icon' => 'bi-folder-fill', 'type' => 'other'];
    }

    protected function parseHeaderBuffer(string $headerBuffer, array &$msg): void
    {
        $headers = $this->extractHeaders($headerBuffer);

        if (!empty($headers['subject'])) {
            $msg['subject'] = $this->decodeMimeStr($headers['subject']);
        }
        if (!empty($headers['from'])) {
            $fromParsed = $this->parseAddress($headers['from']);
            $msg['from_name'] = $fromParsed['name'] ?: $fromParsed['email'];
            $msg['from_email'] = $fromParsed['email'];
        }
        if (!empty($headers['to'])) {
            $msg['to'] = $this->decodeMimeStr($headers['to']);
        }
        if (!empty($headers['date'])) {
            $msg['date'] = $headers['date'];
            try {
                $cDate = \Carbon\Carbon::parse($headers['date']);
                $msg['date_formatted'] = $cDate->isToday() 
                    ? $cDate->format('h:i A') 
                    : ($cDate->isCurrentYear() ? $cDate->format('M d') : $cDate->format('d/m/Y'));
            } catch (\Throwable $e) {
                $msg['date_formatted'] = substr($headers['date'], 0, 16);
            }
        }
    }

    protected function parseFullMimeMessage(int $id, string $rawMessage): array
    {
        // Split header and body
        $parts = preg_split("/\r?\n\r?\n/", $rawMessage, 2);
        $headerSection = $parts[0] ?? '';
        $bodySection = $parts[1] ?? '';

        $headers = $this->extractHeaders($headerSection);

        $fromParsed = $this->parseAddress($headers['from'] ?? '');
        $subject = $this->decodeMimeStr($headers['subject'] ?? '(No Subject)');

        $contentType = $headers['content-type'] ?? 'text/plain';
        $boundary = $this->extractBoundary($contentType);

        $htmlBody = '';
        $textBody = '';
        $attachments = [];

        if ($boundary) {
            $this->parseMultipart($bodySection, $boundary, $htmlBody, $textBody, $attachments);
        } else {
            // Single part message
            $encoding = strtolower(trim($headers['content-transfer-encoding'] ?? '7bit'));
            $decoded = $this->decodeBodyContent($bodySection, $encoding);
            $charset = $this->extractCharset($contentType);
            $decoded = $this->convertToUtf8($decoded, $charset);

            if (str_contains(strtolower($contentType), 'text/html')) {
                $htmlBody = $decoded;
            } else {
                $textBody = $decoded;
            }
        }

        // Fallbacks
        if (empty($htmlBody) && !empty($textBody)) {
            $htmlBody = nl2br(htmlspecialchars($textBody, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
        }

        // Sanitize HTML body for security against XSS while keeping safe styles
        $sanitizedHtml = $this->sanitizeEmailHtml($htmlBody);

        return [
            'id' => $id,
            'subject' => $subject,
            'from_name' => $fromParsed['name'] ?: $fromParsed['email'],
            'from_email' => $fromParsed['email'],
            'to' => $this->decodeMimeStr($headers['to'] ?? ''),
            'cc' => $this->decodeMimeStr($headers['cc'] ?? ''),
            'bcc' => $this->decodeMimeStr($headers['bcc'] ?? ''),
            'reply_to' => $headers['reply-to'] ?? $fromParsed['email'],
            'message_id' => $headers['message-id'] ?? '',
            'in_reply_to' => $headers['in-reply-to'] ?? '',
            'references' => $headers['references'] ?? '',
            'date' => $headers['date'] ?? '',
            'html_body' => $sanitizedHtml,
            'text_body' => $textBody,
            'attachments' => $attachments,
            'size' => strlen($rawMessage),
        ];
    }

    protected function parseMultipart(string $body, string $boundary, string &$htmlBody, string &$textBody, array &$attachments): void
    {
        $delimiter = '--' . $boundary;
        $rawParts = explode($delimiter, $body);

        foreach ($rawParts as $part) {
            $part = trim($part);
            if ($part === '' || $part === '--') continue;

            $split = preg_split("/\r?\n\r?\n/", $part, 2);
            $pHeaderStr = $split[0] ?? '';
            $pContent = $split[1] ?? '';

            $pHeaders = $this->extractHeaders($pHeaderStr);
            $pContentType = strtolower($pHeaders['content-type'] ?? 'text/plain');
            $pDisposition = strtolower($pHeaders['content-disposition'] ?? '');
            $pEncoding = strtolower(trim($pHeaders['content-transfer-encoding'] ?? '7bit'));

            // Nested boundary
            $subBoundary = $this->extractBoundary($pHeaders['content-type'] ?? '');
            if ($subBoundary) {
                $this->parseMultipart($pContent, $subBoundary, $htmlBody, $textBody, $attachments);
                continue;
            }

            // Check if attachment
            $filename = $this->extractFilename($pHeaders);
            if ($filename || str_contains($pDisposition, 'attachment')) {
                $filename = $filename ?: ('attachment_' . (count($attachments) + 1));
                $rawBinary = $this->decodeBodyContent($pContent, $pEncoding);
                $attachments[] = [
                    'index' => count($attachments),
                    'filename' => $this->decodeMimeStr($filename),
                    'content_type' => explode(';', $pContentType)[0] ?? 'application/octet-stream',
                    'size' => strlen($rawBinary),
                    'data' => base64_encode($rawBinary), // Base64 encoded for download
                ];
                continue;
            }

            // Text / HTML Content
            $decoded = $this->decodeBodyContent($pContent, $pEncoding);
            $charset = $this->extractCharset($pHeaders['content-type'] ?? '');
            $decoded = $this->convertToUtf8($decoded, $charset);

            if (str_contains($pContentType, 'text/html') && empty($htmlBody)) {
                $htmlBody = $decoded;
            } elseif (str_contains($pContentType, 'text/plain') && empty($textBody)) {
                $textBody = $decoded;
            }
        }
    }

    protected function extractHeaders(string $headerStr): array
    {
        $lines = preg_split("/\r?\n/", $headerStr);
        $headers = [];
        $currentKey = '';

        foreach ($lines as $line) {
            if (preg_match('/^([a-zA-Z0-9\-_]+):\s*(.*)$/', $line, $matches)) {
                $currentKey = strtolower($matches[1]);
                $headers[$currentKey] = trim($matches[2]);
            } elseif (!empty($currentKey) && (str_starts_with($line, ' ') || str_starts_with($line, "\t"))) {
                // Continuation line
                $headers[$currentKey] .= ' ' . trim($line);
            }
        }

        return $headers;
    }

    protected function extractBoundary(string $contentType): ?string
    {
        if (preg_match('/boundary="([^"]+)"/i', $contentType, $m)) {
            return $m[1];
        }
        if (preg_match('/boundary=([^;\s]+)/i', $contentType, $m)) {
            return trim($m[1], '"');
        }
        return null;
    }

    protected function extractFilename(array $headers): ?string
    {
        $disp = $headers['content-disposition'] ?? '';
        $type = $headers['content-type'] ?? '';

        if (preg_match('/filename\*?=(?:UTF-8\'\')?"([^"]+)"/i', $disp, $m)) {
            return $m[1];
        }
        if (preg_match('/filename\*?=(?:UTF-8\'\')?([^;\s]+)/i', $disp, $m)) {
            return trim($m[1], '"');
        }
        if (preg_match('/name\*?=(?:UTF-8\'\')?"([^"]+)"/i', $type, $m)) {
            return $m[1];
        }
        if (preg_match('/name\*?=(?:UTF-8\'\')?([^;\s]+)/i', $type, $m)) {
            return trim($m[1], '"');
        }
        return null;
    }

    protected function extractCharset(string $contentType): string
    {
        if (preg_match('/charset="([^"]+)"/i', $contentType, $m)) {
            return $m[1];
        }
        if (preg_match('/charset=([^;\s]+)/i', $contentType, $m)) {
            return trim($m[1], '"');
        }
        return 'UTF-8';
    }

    protected function decodeBodyContent(string $content, string $encoding): string
    {
        switch ($encoding) {
            case 'base64':
                return (string) base64_decode(preg_replace('/\s+/', '', $content));
            case 'quoted-printable':
                return quoted_printable_decode($content);
            default:
                return $content;
        }
    }

    protected function convertToUtf8(string $string, string $fromCharset): string
    {
        $fromCharset = strtoupper(trim($fromCharset));
        if ($fromCharset === 'UTF-8' || empty($fromCharset) || $fromCharset === 'US-ASCII') {
            return $string;
        }

        try {
            if (function_exists('mb_convert_encoding')) {
                return mb_convert_encoding($string, 'UTF-8', $fromCharset);
            }
            if (function_exists('iconv')) {
                return (string) @iconv($fromCharset, 'UTF-8//IGNORE', $string);
            }
        } catch (\Throwable $e) {}

        return $string;
    }

    protected function decodeMimeStr(string $string): string
    {
        if (empty($string)) return '';

        if (function_exists('mb_decode_mimeheader')) {
            return mb_decode_mimeheader($string);
        }
        if (function_exists('iconv_mime_decode')) {
            return iconv_mime_decode($string, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8');
        }

        return $string;
    }

    protected function parseAddress(string $raw): array
    {
        $name = '';
        $email = '';

        if (preg_match('/(.*)<([^>]+)>/', $raw, $m)) {
            $name = trim($this->decodeMimeStr(trim($m[1], '" ')));
            $email = trim($m[2]);
        } else {
            $email = trim($raw);
        }

        return ['name' => $name, 'email' => $email];
    }

    protected function sanitizeEmailHtml(string $html): string
    {
        if (empty($html)) return '';

        // Strip dangerous tags like <script>, <object>, <embed>, <iframe>, <form>, <link>
        $cleaned = preg_replace('/<\s*(script|object|embed|iframe|form|link|meta)\b[^>]*>.*?<\s*\/\s*\1\s*>/is', '', $html);
        $cleaned = preg_replace('/<\s*(script|object|embed|iframe|form|link|meta)\b[^>]*>/is', '', $cleaned);

        // Strip inline JavaScript event handlers (e.g. onload, onerror, onclick)
        $cleaned = preg_replace('/\s+on[a-zA-Z]+\s*=\s*(["\']).*?\1/i', '', $cleaned);
        $cleaned = preg_replace('/\s+on[a-zA-Z]+\s*=\s*[^>\s]+/i', '', $cleaned);

        // Strip javascript: URLs
        $cleaned = preg_replace('/href\s*=\s*(["\'])javascript:.*?\1/i', 'href="#"', $cleaned);

        // Force links to open safely in new tab
        $cleaned = preg_replace('/<a\s+(?!.*?target=)/i', '<a target="_blank" rel="noopener noreferrer" ', $cleaned);

        return $cleaned;
    }

    protected function sendCommand(string $command): array
    {
        $this->tagCounter++;
        $tag = 'TAG' . $this->tagCounter;
        $fullCommand = "{$tag} {$command}\r\n";

        fwrite($this->stream, $fullCommand);

        $lines = [];
        $status = 'UNKNOWN';
        $message = '';
        $rawFirst = '';

        while (($line = $this->readLine()) !== null) {
            if ($rawFirst === '') {
                $rawFirst = $line;
            }

            if (str_starts_with($line, $tag . ' ')) {
                // Completed command response line
                $parts = explode(' ', substr($line, strlen($tag) + 1), 2);
                $status = strtoupper(trim($parts[0]));
                $message = trim($parts[1] ?? '');
                break;
            }

            // ⚡ Handle IMAP Literals: {1234} indicates the server is streaming exact bytes
            if (preg_match('/\{(\d+)\}$/', $line, $litMatch)) {
                $litLen = (int) $litMatch[1];
                $literalData = '';
                $remaining = $litLen;
                while ($remaining > 0 && !feof($this->stream)) {
                    $readSize = min($remaining, 65536);
                    $chunk = fread($this->stream, $readSize);
                    if ($chunk === false || $chunk === '') {
                        break;
                    }
                    $literalData .= $chunk;
                    $remaining -= strlen($chunk);
                }
                $lines[] = $line;
                $lines[] = $literalData;
                continue;
            }

            $lines[] = $line;
        }

        return [
            'status' => $status,
            'message' => $message,
            'lines' => $lines,
            'raw_first' => $rawFirst,
        ];
    }

    protected function readLine(): ?string
    {
        if (!$this->stream || !is_resource($this->stream) || feof($this->stream)) {
            return null;
        }
        $line = fgets($this->stream, 8192);
        return $line !== false ? rtrim($line, "\r\n") : null;
    }

    protected function escape(string $str): string
    {
        return '"' . addcslashes($str, '\\"') . '"';
    }
}
