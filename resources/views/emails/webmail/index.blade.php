@extends('layouts.app')

@section('content')
<div class="container-fluid py-3 px-md-4">
  
  <!-- 🌟 Top Header -->
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3 pb-2 border-bottom" style="border-color: var(--cc-border) !important;">
    <div class="d-flex align-items-center gap-2.5">
      <div class="p-2.5 rounded-3" style="background: rgba(230, 176, 74, 0.15); color: #d97706;">
        <i class="bi bi-envelope-at-fill fs-4"></i>
      </div>
      <div>
        <h4 class="fw-bold mb-0 d-flex align-items-center gap-2" style="color: var(--cc-text-primary);">
          Hostinger Webmail
          <span class="badge rounded-pill font-monospace px-2.5 py-1" style="font-size: 11px; background: #E6B04A; color: #1e293b;">{{ $mailboxUsername }}</span>
        </h4>
        <small style="color: var(--cc-text-secondary);">Official Crown Carz Business Mailbox &bull; Real-time IMAP/SMTP Synchronized</small>
      </div>
    </div>

    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('emails.index', ['folder' => $currentFolder, 'refresh' => 1]) }}" class="btn btn-sm d-flex align-items-center gap-1.5 px-3 py-1.5 rounded-3 shadow-xs webmail-btn-outline" title="Force Sync with Hostinger IMAP">
        <i class="bi bi-arrow-clockwise"></i>
        <span>Refresh Mailbox</span>
      </a>
      <button type="button" class="btn btn-warning btn-sm d-flex align-items-center gap-1.5 px-3 py-1.5 rounded-3 shadow-xs fw-bold" onclick="openComposeModal()" style="background-color: #E6B04A; border-color: #E6B04A; color: #1e293b;">
        <i class="bi bi-pencil-square"></i>
        <span>Compose Email</span>
      </button>
    </div>
  </div>

  @if(!empty($errorMessage))
    <div class="alert alert-danger d-flex align-items-start gap-3 rounded-3 shadow-sm border-0 mb-3" role="alert">
      <i class="bi bi-exclamation-triangle-fill fs-4 flex-shrink-0 text-danger"></i>
      <div>
        <h6 class="fw-bold mb-1">Hostinger Connection Notice</h6>
        <p class="mb-1 small">{{ $errorMessage }}</p>
        <small class="text-muted">Please check your <code>.env</code> file for <code>HOSTINGER_WEBMAIL_IMAP_PASSWORD</code> or contact your server administrator.</small>
      </div>
    </div>
  @endif

  <!-- 📬 Main Webmail Application Card -->
  <div class="card border-0 shadow-sm rounded-4 overflow-hidden webmail-main-card" style="min-height: 750px;">
    <div class="d-flex flex-row flex-nowrap" style="min-height: 750px;">
      
      <!-- 📁 1. Left Sidebar: Mail Folders -->
      <div class="webmail-folders-col p-3 d-flex flex-column">
        <button type="button" class="btn btn-warning w-100 fw-bold py-2 mb-3 rounded-3 shadow-xs d-flex align-items-center justify-content-center gap-2" onclick="openComposeModal()" style="background-color: #E6B04A; border-color: #E6B04A; color: #1e293b;">
          <i class="bi bi-plus-circle-fill"></i>
          <span>New Message</span>
        </button>

        <div class="text-uppercase fw-bold px-2 mb-2" style="font-size: 11px; letter-spacing: 0.6px; color: var(--cc-text-muted);">
          Folders
        </div>

        <ul class="nav nav-pills flex-column gap-1 mb-auto">
          @forelse($folders as $f)
            @php
              $isActive = (strtolower($f['name']) === strtolower($currentFolder));
            @endphp
            <li class="nav-item">
              <a href="{{ route('emails.index', ['folder' => $f['name']]) }}" 
                 class="nav-link py-2 px-2.5 rounded-3 d-flex align-items-center justify-content-between webmail-folder-link {{ $isActive ? 'active' : '' }}"
                 title="{{ $f['name'] }}">
                <span class="d-flex align-items-center gap-2 text-truncate me-2">
                  <i class="bi {{ $f['icon'] }} fs-6"></i>
                  <span class="text-truncate" style="font-size: 12.5px;">{{ $f['display_name'] }}</span>
                </span>
                @if($f['unseen'] > 0)
                  <span class="badge rounded-pill font-monospace {{ $isActive ? 'bg-dark text-warning' : 'bg-warning text-dark' }}" style="font-size: 10px; padding: 2px 7px;">
                    {{ $f['unseen'] }}
                  </span>
                @endif
              </a>
            </li>
          @empty
            <li class="nav-item">
              <a href="{{ route('emails.index', ['folder' => 'INBOX']) }}" class="nav-link active webmail-folder-link py-2 px-2.5 rounded-3 d-flex align-items-center gap-2">
                <i class="bi bi-inbox-fill"></i>
                <span>Inbox</span>
              </a>
            </li>
          @endforelse
        </ul>

        <div class="pt-3 mt-3 border-top small" style="border-color: var(--cc-border) !important; color: var(--cc-text-muted); font-size: 11px;">
          <div class="d-flex align-items-center gap-1.5"><i class="bi bi-shield-lock-fill text-success"></i><span>SSL Encrypted</span></div>
          <div class="text-truncate mt-0.5" style="color: var(--cc-text-secondary);">imap.hostinger.com:993</div>
        </div>
      </div>

      <!-- 📨 2. Center Column: Email Messages List -->
      <div class="webmail-messages-col d-flex flex-column" id="emailListColumn">
        
        <!-- Search & Control Header -->
        <div class="p-3 border-bottom" style="border-color: var(--cc-border) !important; background: var(--cc-bg-card-subtle);">
          <form method="GET" action="{{ route('emails.index') }}" class="mb-2">
            <input type="hidden" name="folder" value="{{ $currentFolder }}">
            <div class="input-group input-group-sm">
              <span class="input-group-text border-end-0 webmail-input-addon"><i class="bi bi-search"></i></span>
              <input type="text" name="q" value="{{ $searchQuery }}" class="form-control border-start-0 webmail-input" placeholder="Search sender, subject...">
              @if(!empty($searchQuery))
                <a href="{{ route('emails.index', ['folder' => $currentFolder]) }}" class="btn btn-outline-secondary webmail-btn-outline"><i class="bi bi-x"></i></a>
              @endif
              <button class="btn btn-warning fw-bold px-3" type="submit" style="background-color: #E6B04A; border-color: #E6B04A; color: #1e293b;">Search</button>
            </div>
          </form>

          <div class="d-flex justify-content-between align-items-center small" style="color: var(--cc-text-secondary);">
            <span class="fw-bold text-truncate" style="color: var(--cc-text-primary);">
              <i class="bi bi-folder2-open me-1 text-warning"></i>{{ $currentFolder }} <span class="badge bg-secondary bg-opacity-25 text-body rounded-pill ms-1 font-monospace" style="font-size: 10px;">{{ $total }}</span>
            </span>
            <span class="font-monospace" style="font-size: 11px;">Page {{ $currentPage }} of {{ $totalPages }}</span>
          </div>
        </div>

        <!-- Emails Scrollable List -->
        <div class="flex-grow-1 overflow-y-auto webmail-list-scroll" style="max-height: calc(100vh - 275px);">
          @forelse($messages as $msg)
            <div class="email-item-row p-3 border-bottom transition-all {{ !$msg['is_seen'] ? 'email-item-unread' : '' }}" 
                 id="email-item-{{ $msg['id'] }}" 
                 onclick="loadEmailDetails('{{ urlencode($currentFolder) }}', {{ $msg['id'] }})">
              
              <div class="d-flex justify-content-between align-items-center mb-1">
                <div class="d-flex align-items-center gap-1.5 text-truncate me-2">
                  @if(!$msg['is_seen'])
                    <span class="email-unread-dot" title="Unread"></span>
                  @endif
                  <span class="text-truncate email-sender-name {{ !$msg['is_seen'] ? 'fw-bold' : '' }}">
                    {{ $msg['from_name'] ?: $msg['from_email'] }}
                  </span>
                </div>
                <small class="font-monospace text-nowrap email-date-badge">
                  {{ $msg['date_formatted'] }}
                </small>
              </div>

              <div class="text-truncate email-subject-text mb-1 {{ !$msg['is_seen'] ? 'fw-bold' : '' }}">
                {{ $msg['subject'] }}
              </div>

              <div class="d-flex align-items-center justify-content-between">
                <small class="text-truncate email-sender-email" style="max-width: 80%;">
                  {{ $msg['from_email'] }}
                </small>
                <div class="d-flex align-items-center gap-2">
                  @if($msg['has_attachments'])
                    <i class="bi bi-paperclip text-warning" title="Contains Attachment" style="font-size: 13px;"></i>
                  @endif
                  <button type="button" class="btn btn-link p-0 text-secondary email-star-btn" onclick="toggleEmailStar(event, '{{ urlencode($currentFolder) }}', {{ $msg['id'] }}, this)" title="Star">
                    <i class="bi {{ $msg['is_starred'] ? 'bi-star-fill text-warning' : 'bi-star' }}" style="font-size: 13px;"></i>
                  </button>
                </div>
              </div>

            </div>
          @empty
            <div class="text-center py-5" style="color: var(--cc-text-muted);">
              <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
              <div class="fw-semibold">No emails found</div>
              <small>This mailbox folder is currently empty.</small>
            </div>
          @endforelse
        </div>

        <!-- Pagination Footer -->
        @if($totalPages > 1)
          <div class="p-2 border-top d-flex justify-content-between align-items-center" style="border-color: var(--cc-border) !important; background: var(--cc-bg-card-subtle);">
            @if($currentPage > 1)
              <a href="{{ route('emails.index', ['folder' => $currentFolder, 'page' => $currentPage - 1, 'q' => $searchQuery]) }}" class="btn btn-sm py-1 px-2.5 webmail-btn-outline" style="font-size: 11px;">
                <i class="bi bi-chevron-left me-1"></i>Prev
              </a>
            @else
              <div></div>
            @endif

            <span class="small font-monospace" style="font-size: 11px; color: var(--cc-text-secondary);">{{ $currentPage }} / {{ $totalPages }}</span>

            @if($currentPage < $totalPages)
              <a href="{{ route('emails.index', ['folder' => $currentFolder, 'page' => $currentPage + 1, 'q' => $searchQuery]) }}" class="btn btn-sm py-1 px-2.5 webmail-btn-outline" style="font-size: 11px;">
                Next<i class="bi bi-chevron-right ms-1"></i>
              </a>
            @else
              <div></div>
            @endif
          </div>
        @endif

      </div>

      <!-- 📖 3. Right Column: Active Email Reader Pane -->
      <div class="webmail-reader-col d-flex flex-column position-relative" id="emailDetailPane">
        
        <!-- Placeholder / Empty State -->
        <div id="emailDetailPlaceholder" class="m-auto text-center py-5 px-3">
          <div class="p-4 rounded-circle d-inline-block mb-3" style="background: rgba(230, 176, 74, 0.12);">
            <i class="bi bi-envelope-open fs-1" style="color: #d97706;"></i>
          </div>
          <h5 class="fw-bold mb-1" style="color: var(--cc-text-primary);">Select an email to read</h5>
          <p class="small mb-0" style="color: var(--cc-text-secondary); max-width: 360px;">Click on any message from the list to view its complete content, sender details, and attachments.</p>
        </div>

        <!-- Loading Spinner -->
        <div id="emailDetailLoading" class="m-auto text-center py-5 d-none">
          <div class="spinner-border text-warning" role="status"></div>
          <div class="small mt-2" style="color: var(--cc-text-secondary);">Loading email securely from Hostinger...</div>
        </div>

        <!-- Active Email View Container -->
        <div id="emailDetailContent" class="d-none d-flex flex-column h-100">
          
          <!-- Reader Action Toolbar -->
          <div class="p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2" style="border-color: var(--cc-border) !important; background: var(--cc-bg-card-subtle);">
            <div class="d-flex align-items-center gap-2">
              <button type="button" class="btn btn-sm btn-outline-warning d-flex align-items-center gap-1.5 px-3 py-1.5 fw-semibold" onclick="replyCurrentEmail(false)">
                <i class="bi bi-reply-fill"></i><span>Reply</span>
              </button>
              <button type="button" class="btn btn-sm webmail-btn-outline d-flex align-items-center gap-1.5 px-2.5 py-1.5" onclick="replyCurrentEmail(true)">
                <i class="bi bi-reply-all-fill"></i><span>Reply All</span>
              </button>
              <button type="button" class="btn btn-sm webmail-btn-outline d-flex align-items-center gap-1.5 px-2.5 py-1.5" onclick="forwardCurrentEmail()">
                <i class="bi bi-forward-fill"></i><span>Forward</span>
              </button>
            </div>

            <div class="d-flex align-items-center gap-2">
              <button type="button" class="btn btn-sm webmail-btn-outline py-1.5 px-2.5" id="btnToggleReadStatus" onclick="toggleCurrentReadStatus()" title="Mark as Unread">
                <i class="bi bi-envelope"></i>
              </button>
              <button type="button" class="btn btn-sm btn-outline-danger py-1.5 px-2.5" onclick="deleteCurrentEmail()" title="Move to Trash">
                <i class="bi bi-trash"></i>
              </button>
            </div>
          </div>

          <!-- Email Headers & Subject -->
          <div class="p-4 border-bottom" style="border-color: var(--cc-border) !important;">
            <h5 class="fw-bold mb-3" id="viewEmailSubject" style="color: var(--cc-text-primary); line-height: 1.4;">--</h5>
            
            <div class="d-flex align-items-start justify-content-between gap-3">
              <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle fw-bold d-flex align-items-center justify-content-center flex-shrink-0 shadow-xs" id="viewSenderAvatar" style="width: 44px; height: 44px; font-size: 16px; background: #E6B04A; color: #1e293b;">
                  CC
                </div>
                <div>
                  <div class="fw-bold" id="viewSenderName" style="color: var(--cc-text-primary); font-size: 14px;">--</div>
                  <div class="small" style="color: var(--cc-text-secondary); font-size: 12px;">
                    From: <span class="font-monospace fw-semibold" id="viewSenderEmail" style="color: #d97706;">--</span>
                  </div>
                  <div class="small" style="color: var(--cc-text-muted); font-size: 11px;">
                    To: <span id="viewRecipientTo">--</span>
                  </div>
                </div>
              </div>

              <div class="text-end small font-monospace" id="viewEmailDate" style="color: var(--cc-text-secondary); font-size: 11.5px;">
                --
              </div>
            </div>
          </div>

          <!-- Attachments List (If Any) -->
          <div id="viewAttachmentsContainer" class="p-3 border-bottom d-none" style="border-color: var(--cc-border) !important; background: var(--cc-bg-card-subtle);">
            <div class="d-flex align-items-center gap-2 mb-2 small fw-bold" style="color: #d97706;">
              <i class="bi bi-paperclip"></i>
              <span>Attachments (<span id="viewAttachmentsCount">0</span>)</span>
            </div>
            <div class="d-flex flex-wrap gap-2" id="viewAttachmentsList">
              <!-- Dynamically populated attachment chips -->
            </div>
          </div>

          <!-- Email HTML Content Container -->
          <div class="flex-grow-1 p-3 d-flex flex-column" style="background: var(--cc-bg-card);">
            <div class="w-100 flex-grow-1 rounded-3 overflow-hidden shadow-xs border" style="border-color: var(--cc-border) !important; background: #ffffff;">
              <iframe id="viewEmailIframe" 
                      class="w-100 h-100 border-0" 
                      sandbox="allow-same-origin allow-popups" 
                      style="min-height: 480px; width: 100%; display: block;"></iframe>
            </div>
          </div>

        </div>

      </div>

    </div>
  </div>
</div>

<!-- =========================================================================
     ✍️ COMPOSE / REPLY / FORWARD EMAIL MODAL
     ========================================================================= -->
<div class="modal fade" id="composeEmailModal" tabindex="-1" aria-labelledby="composeEmailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4 webmail-modal-content">
      <div class="modal-header border-bottom py-3" style="border-color: var(--cc-border) !important;">
        <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="composeEmailModalLabel" style="color: var(--cc-text-primary);">
          <i class="bi bi-pencil-square text-warning"></i>
          <span>New Message</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="composeEmailForm" onsubmit="handleSendEmail(event)" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="in_reply_to" id="composeInReplyTo">
        <input type="hidden" name="references" id="composeReferences">

        <div class="modal-body p-4 d-flex flex-column gap-3">
          
          <!-- From Badge -->
          <div class="d-flex align-items-center justify-content-between p-2.5 rounded-3 small border" style="background: var(--cc-bg-card-subtle); border-color: var(--cc-border) !important;">
            <span style="color: var(--cc-text-secondary);">Sending From:</span>
            <span class="fw-bold font-monospace" style="color: #d97706;">{{ $mailboxUsername }} &bull; Hostinger SMTP</span>
          </div>

          <!-- To Input -->
          <div>
            <label class="form-label small fw-bold mb-1" style="color: var(--cc-text-secondary);">To <span class="text-danger">*</span></label>
            <input type="text" name="to" id="composeTo" class="form-control webmail-input" placeholder="recipient@example.com, another@example.com" required>
          </div>

          <!-- CC & BCC Toggles -->
          <div class="d-flex justify-content-end gap-3" style="font-size: 11.5px;">
            <a href="javascript:void(0)" class="text-decoration-none fw-semibold" style="color: var(--cc-text-secondary);" onclick="document.getElementById('composeCcWrapper').classList.toggle('d-none')">+ CC</a>
            <a href="javascript:void(0)" class="text-decoration-none fw-semibold" style="color: var(--cc-text-secondary);" onclick="document.getElementById('composeBccWrapper').classList.toggle('d-none')">+ BCC</a>
          </div>

          <!-- CC Input (Hidden by default) -->
          <div id="composeCcWrapper" class="d-none">
            <label class="form-label small fw-bold mb-1" style="color: var(--cc-text-secondary);">CC</label>
            <input type="text" name="cc" id="composeCc" class="form-control form-control-sm webmail-input" placeholder="cc@example.com">
          </div>

          <!-- BCC Input (Hidden by default) -->
          <div id="composeBccWrapper" class="d-none">
            <label class="form-label small fw-bold mb-1" style="color: var(--cc-text-secondary);">BCC</label>
            <input type="text" name="bcc" id="composeBcc" class="form-control form-control-sm webmail-input" placeholder="bcc@example.com">
          </div>

          <!-- Subject -->
          <div>
            <label class="form-label small fw-bold mb-1" style="color: var(--cc-text-secondary);">Subject <span class="text-danger">*</span></label>
            <input type="text" name="subject" id="composeSubject" class="form-control webmail-input" placeholder="Enter email subject" required>
          </div>

          <!-- Message Body -->
          <div>
            <label class="form-label small fw-bold mb-1" style="color: var(--cc-text-secondary);">Message Body <span class="text-danger">*</span></label>
            <textarea name="body" id="composeBody" rows="9" class="form-control webmail-input font-monospace" placeholder="Type your message here..." style="font-size: 13px; line-height: 1.5;" required></textarea>
          </div>

          <!-- Attachments File Input -->
          <div>
            <label class="form-label small fw-bold mb-1" style="color: var(--cc-text-secondary);"><i class="bi bi-paperclip me-1"></i>Attach Files (Max 20MB)</label>
            <input type="file" name="attachments[]" id="composeAttachments" class="form-control form-control-sm webmail-input" multiple>
          </div>

        </div>

        <div class="modal-footer border-top py-3" style="border-color: var(--cc-border) !important;">
          <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" id="btnSubmitSendEmail" class="btn btn-warning btn-sm px-4 fw-bold d-flex align-items-center gap-1.5" style="background-color: #E6B04A; border-color: #E6B04A; color: #1e293b;">
            <i class="bi bi-send-fill"></i>
            <span>Send Email</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<style>
/* ==========================================================
   🎨 THEME-AWARE WEBMAIL STYLES (LIGHT & DARK MODE)
   ========================================================== */
.webmail-main-card {
  background-color: var(--cc-bg-card) !important;
  border: 1px solid var(--cc-border) !important;
}

/* 📁 Folders Column */
.webmail-folders-col {
  width: 230px;
  min-width: 200px;
  max-width: 250px;
  flex-shrink: 0;
  border-right: 1px solid var(--cc-border) !important;
  background-color: var(--cc-bg-card-subtle) !important;
}

.webmail-folder-link {
  color: var(--cc-text-primary) !important;
  font-size: 12.5px;
  font-weight: 500;
  transition: all 0.15s ease;
}
.webmail-folder-link:hover {
  background-color: var(--cc-table-hover) !important;
  color: #d97706 !important;
}
.webmail-folder-link.active {
  background-color: #E6B04A !important;
  color: #1e293b !important;
  font-weight: 700 !important;
  box-shadow: 0 2px 6px rgba(230, 176, 74, 0.25) !important;
}

/* 📨 Messages Column */
.webmail-messages-col {
  width: 380px;
  min-width: 320px;
  max-width: 420px;
  flex-shrink: 0;
  border-right: 1px solid var(--cc-border) !important;
  background-color: var(--cc-bg-card) !important;
}

.email-item-row {
  cursor: pointer;
  border-color: var(--cc-border-subtle) !important;
  transition: background-color 0.15s ease;
  position: relative;
}
.email-item-row:hover {
  background-color: var(--cc-table-hover) !important;
}
.email-item-row.email-item-unread {
  background-color: rgba(230, 176, 74, 0.08) !important;
}
.email-item-row.active-email-row {
  background-color: rgba(230, 176, 74, 0.18) !important;
  border-left: 4px solid #E6B04A !important;
}

.email-unread-dot {
  display: inline-block;
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background-color: #E6B04A;
  box-shadow: 0 0 4px rgba(230, 176, 74, 0.8);
  flex-shrink: 0;
}

.email-sender-name {
  color: var(--cc-text-primary);
  font-size: 13px;
}
.email-subject-text {
  color: var(--cc-text-primary);
  font-size: 12.5px;
}
.email-sender-email {
  color: var(--cc-text-secondary);
  font-size: 11px;
}
.email-date-badge {
  color: var(--cc-text-secondary);
  font-size: 11px;
}

/* 📖 Reader Column */
.webmail-reader-col {
  flex-grow: 1;
  min-width: 400px;
  background-color: var(--cc-bg-card) !important;
}

/* 🛠️ Common Controls */
.webmail-btn-outline {
  border: 1px solid var(--cc-border) !important;
  color: var(--cc-text-primary) !important;
  background-color: var(--cc-bg-card-subtle) !important;
}
.webmail-btn-outline:hover {
  background-color: var(--cc-table-hover) !important;
  border-color: #E6B04A !important;
  color: #d97706 !important;
}

.webmail-input {
  background-color: var(--cc-input-bg) !important;
  color: var(--cc-input-color) !important;
  border: 1px solid var(--cc-input-border) !important;
}
.webmail-input:focus {
  border-color: #E6B04A !important;
  box-shadow: 0 0 0 0.2rem rgba(230, 176, 74, 0.25) !important;
}
.webmail-input-addon {
  background-color: var(--cc-bg-card-subtle) !important;
  border: 1px solid var(--cc-input-border) !important;
  color: var(--cc-text-secondary) !important;
}

.webmail-modal-content {
  background-color: var(--cc-modal-bg) !important;
  color: var(--cc-text-primary) !important;
}

/* Custom Slim Scrollbars */
.webmail-list-scroll::-webkit-scrollbar {
  width: 5px;
}
.webmail-list-scroll::-webkit-scrollbar-thumb {
  background: var(--cc-border);
  border-radius: 4px;
}
</style>

<script>
let currentLoadedMessage = null;
let currentFolderGlobal = '{{ $currentFolder }}';

// 🚀 Open Compose Modal with blank fields
function openComposeModal() {
  document.getElementById('composeEmailForm').reset();
  document.getElementById('composeEmailModalLabel').innerHTML = '<i class="bi bi-pencil-square text-warning"></i><span>New Message</span>';
  document.getElementById('composeInReplyTo').value = '';
  document.getElementById('composeReferences').value = '';
  document.getElementById('composeCcWrapper').classList.add('d-none');
  document.getElementById('composeBccWrapper').classList.add('d-none');

  const modal = new bootstrap.Modal(document.getElementById('composeEmailModal'));
  modal.show();
}

// 📨 Load Email Details via AJAX
async function loadEmailDetails(folder, id) {
  const placeholder = document.getElementById('emailDetailPlaceholder');
  const loading = document.getElementById('emailDetailLoading');
  const content = document.getElementById('emailDetailContent');

  placeholder.classList.add('d-none');
  content.classList.add('d-none');
  loading.classList.remove('d-none');

  // Highlight active row & remove unread dot
  document.querySelectorAll('.email-item-row').forEach(row => row.classList.remove('active-email-row'));
  const rowEl = document.getElementById(`email-item-${id}`);
  if (rowEl) {
    rowEl.classList.add('active-email-row');
    rowEl.classList.remove('email-item-unread');
    const dot = rowEl.querySelector('.email-unread-dot');
    if (dot) dot.remove();
  }

  try {
    const url = `/admin/emails/message/${id}?folder=${encodeURIComponent(folder)}`;
    const res = await fetch(url, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });

    let data;
    try {
      data = await res.json();
    } catch (e) {
      data = null;
    }

    if (!res.ok || !data || !data.success) {
      const errMsg = (data && data.error) ? data.error : `HTTP ${res.status}: Failed to load email from Hostinger`;
      throw new Error(errMsg);
    }

    const msg = data.message;
    currentLoadedMessage = msg;
    currentFolderGlobal = folder;

    // Populate Headers
    document.getElementById('viewEmailSubject').textContent = msg.subject || '(No Subject)';
    document.getElementById('viewSenderName').textContent = msg.from_name || msg.from_email;
    document.getElementById('viewSenderEmail').textContent = msg.from_email;
    document.getElementById('viewRecipientTo').textContent = msg.to || 'Undisclosed Recipients';
    document.getElementById('viewEmailDate').textContent = msg.date || '';

    // Sender initials avatar
    const initials = (msg.from_name || msg.from_email).substring(0, 2).toUpperCase();
    document.getElementById('viewSenderAvatar').textContent = initials;

    // Attachments
    const attContainer = document.getElementById('viewAttachmentsContainer');
    const attList = document.getElementById('viewAttachmentsList');
    const attCount = document.getElementById('viewAttachmentsCount');

    if (msg.attachments && msg.attachments.length > 0) {
      attContainer.classList.remove('d-none');
      attCount.textContent = msg.attachments.length;
      attList.innerHTML = '';

      msg.attachments.forEach(att => {
        const chip = document.createElement('a');
        chip.href = `/admin/emails/message/${id}/attachment/${att.index}?folder=${encodeURIComponent(folder)}`;
        chip.className = 'btn btn-sm webmail-btn-outline d-flex align-items-center gap-1.5 text-decoration-none py-1 px-2.5 rounded-2';
        chip.innerHTML = `
          <i class="bi bi-file-earmark-arrow-down text-warning"></i>
          <span class="text-truncate" style="max-width: 180px;">${escapeHtml(att.filename)}</span>
          <small class="text-muted font-monospace">(${formatBytes(att.size)})</small>
        `;
        attList.appendChild(chip);
      });
    } else {
      attContainer.classList.add('d-none');
    }

    // Sandboxed HTML Body rendering in iframe
    const iframe = document.getElementById('viewEmailIframe');
    const fullHtml = `
      <!DOCTYPE html>
      <html>
      <head>
        <meta charset="utf-8">
        <style>
          body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; color: #1e293b; padding: 16px; margin: 0; word-wrap: break-word; }
          a { color: #0284c7; }
          img { max-width: 100% !important; height: auto !important; }
          table { max-width: 100% !important; }
        </style>
      </head>
      <body>
        ${msg.html_body || '<p class="text-muted">No content</p>'}
      </body>
      </html>
    `;
    iframe.srcdoc = fullHtml;

    loading.classList.add('d-none');
    content.classList.remove('d-none');

  } catch (err) {
    loading.classList.add('d-none');
    placeholder.classList.remove('d-none');
    Swal.fire({
      icon: 'error',
      title: 'Failed to load email',
      text: err.message,
      confirmButtonColor: '#E6B04A'
    });
  }
}

// ↩️ Reply to active email
function replyCurrentEmail(replyAll = false) {
  if (!currentLoadedMessage) return;
  const msg = currentLoadedMessage;

  openComposeModal();
  document.getElementById('composeEmailModalLabel').innerHTML = `<i class="bi bi-reply-fill text-warning"></i><span>${replyAll ? 'Reply All' : 'Reply'}</span>`;

  document.getElementById('composeTo').value = msg.reply_to || msg.from_email;
  if (replyAll && msg.cc) {
    document.getElementById('composeCcWrapper').classList.remove('d-none');
    document.getElementById('composeCc').value = msg.cc;
  }

  const subj = msg.subject || '';
  document.getElementById('composeSubject').value = subj.startsWith('Re:') ? subj : `Re: ${subj}`;

  document.getElementById('composeInReplyTo').value = msg.message_id || '';
  document.getElementById('composeReferences').value = `${msg.references || ''} ${msg.message_id || ''}`.trim();

  const quoteHeader = `\n\n\n--- On ${msg.date}, ${msg.from_name || msg.from_email} wrote: ---\n> ${msg.text_body ? msg.text_body.replace(/\n/g, '\n> ') : ''}`;
  document.getElementById('composeBody').value = quoteHeader;
}

// ↪️ Forward active email
function forwardCurrentEmail() {
  if (!currentLoadedMessage) return;
  const msg = currentLoadedMessage;

  openComposeModal();
  document.getElementById('composeEmailModalLabel').innerHTML = `<i class="bi bi-forward-fill text-warning"></i><span>Forward Email</span>`;

  document.getElementById('composeTo').value = '';
  const subj = msg.subject || '';
  document.getElementById('composeSubject').value = subj.startsWith('Fwd:') ? subj : `Fwd: ${subj}`;

  const forwardHeader = `\n\n\n---------- Forwarded message ---------\nFrom: ${msg.from_name || ''} <${msg.from_email}>\nDate: ${msg.date}\nSubject: ${msg.subject}\nTo: ${msg.to}\n\n${msg.text_body || ''}`;
  document.getElementById('composeBody').value = forwardHeader;
}

// 🗑️ Delete active email
async function deleteCurrentEmail() {
  if (!currentLoadedMessage) return;
  const id = currentLoadedMessage.id;

  const result = await Swal.fire({
    title: 'Delete this email?',
    text: 'It will be moved to the Trash folder on Hostinger.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Yes, delete it'
  });

  if (!result.isConfirmed) return;

  try {
    const res = await fetch('/admin/emails/delete', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({ id: id, folder: currentFolderGlobal })
    });

    const data = await res.json();
    if (data.success) {
      document.getElementById(`email-item-${id}`)?.remove();
      document.getElementById('emailDetailContent').classList.add('d-none');
      document.getElementById('emailDetailPlaceholder').classList.remove('d-none');
      currentLoadedMessage = null;

      Swal.fire({
        icon: 'success',
        title: 'Deleted',
        text: 'Message moved to Trash.',
        timer: 1500,
        showConfirmButton: false
      });
    } else {
      throw new Error(data.error || 'Failed to delete');
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: err.message, confirmButtonColor: '#E6B04A' });
  }
}

// ✉️ Send Email via Form AJAX
async function handleSendEmail(e) {
  e.preventDefault();
  const form = e.target;
  const btn = document.getElementById('btnSubmitSendEmail');
  const originalHtml = btn.innerHTML;

  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Sending via Hostinger...';

  const formData = new FormData(form);

  try {
    const res = await fetch('/admin/emails/send', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: formData
    });

    const data = await res.json();
    if (data.success) {
      bootstrap.Modal.getInstance(document.getElementById('composeEmailModal'))?.hide();
      form.reset();

      Swal.fire({
        icon: 'success',
        title: 'Sent!',
        text: 'Your email was sent successfully through Hostinger SMTP.',
        confirmButtonColor: '#E6B04A'
      });
    } else {
      throw new Error(data.error || 'Failed to send email');
    }
  } catch (err) {
    Swal.fire({
      icon: 'error',
      title: 'Send Failed',
      text: err.message,
      confirmButtonColor: '#E6B04A'
    });
  } finally {
    btn.disabled = false;
    btn.innerHTML = originalHtml;
  }
}

// ⭐ Toggle Star Flag
async function toggleEmailStar(event, folder, id, btnEl) {
  event.stopPropagation();
  const icon = btnEl.querySelector('i');
  const isCurrentlyStarred = icon.classList.contains('bi-star-fill');
  const nextStarred = !isCurrentlyStarred;

  icon.className = nextStarred ? 'bi bi-star-fill text-warning' : 'bi bi-star text-secondary';

  try {
    await fetch('/admin/emails/star', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({ id: id, folder: folder, starred: nextStarred })
    });
  } catch(e) {}
}

function escapeHtml(text) {
  return String(text || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function formatBytes(bytes) {
  if (bytes === 0) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}
</script>
@endpush
