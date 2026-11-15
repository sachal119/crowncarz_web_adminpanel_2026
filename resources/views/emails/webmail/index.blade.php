@extends('layouts.app')

@section('content')
<div class="container-fluid py-3 px-md-4">
  <!-- Page Header -->
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3 pb-2 border-bottom border-secondary border-opacity-25">
    <div class="d-flex align-items-center gap-2">
      <div class="p-2 rounded-3 text-warning bg-warning bg-opacity-10">
        <i class="bi bi-envelope-at-fill fs-4"></i>
      </div>
      <div>
        <h4 class="fw-bold mb-0 text-white d-flex align-items-center gap-2">
          Hostinger Webmail
          <span class="badge bg-warning text-dark font-monospace px-2 py-0.5" style="font-size: 11px;">{{ $mailboxUsername }}</span>
        </h4>
        <small class="text-secondary">Official Crown Carz Business Mailbox &bull; IMAP/SMTP Synchronized</small>
      </div>
    </div>

    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('emails.index', ['folder' => $currentFolder, 'refresh' => 1]) }}" class="btn btn-outline-warning btn-sm d-flex align-items-center gap-1.5 px-3 py-1.5 rounded-3 shadow-xs" title="Force Sync with Hostinger IMAP">
        <i class="bi bi-arrow-clockwise"></i>
        <span>Refresh Mailbox</span>
      </a>
      <button type="button" class="btn btn-warning btn-sm d-flex align-items-center gap-1.5 px-3 py-1.5 rounded-3 shadow-xs fw-bold" onclick="openComposeModal()">
        <i class="bi bi-pencil-square"></i>
        <span>Compose Email</span>
      </button>
    </div>
  </div>

  @if(!empty($errorMessage))
    <div class="alert alert-danger d-flex align-items-start gap-3 rounded-3 shadow-sm border-0 mb-4" role="alert">
      <i class="bi bi-exclamation-triangle-fill fs-4 flex-shrink-0 text-danger"></i>
      <div>
        <h6 class="fw-bold mb-1">Hostinger Connection Notice</h6>
        <p class="mb-1 small">{{ $errorMessage }}</p>
        <small class="text-muted">Please check your <code>.env</code> file for <code>HOSTINGER_WEBMAIL_IMAP_PASSWORD</code> or contact your server administrator.</small>
      </div>
    </div>
  @endif

  <!-- Main Webmail Application Container -->
  <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="background-color: var(--cc-bg-card, #151d2c); min-height: 720px;">
    <div class="row g-0 flex-nowrap" style="min-height: 720px;">
      
      <!-- 📁 Left Sidebar: Mail Folders -->
      <div class="col-auto col-md-3 col-xl-2 border-end border-secondary border-opacity-25 p-3 d-flex flex-column" style="background: rgba(0,0,0,0.12);">
        <button type="button" class="btn btn-warning w-100 fw-bold py-2 mb-3 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2" onclick="openComposeModal()">
          <i class="bi bi-plus-circle-fill"></i>
          <span class="d-none d-md-inline">New Message</span>
        </button>

        <div class="text-uppercase text-secondary fw-bold small px-2 mb-2 d-none d-md-block" style="font-size: 11px; letter-spacing: 0.5px;">Folders</div>
        <ul class="nav nav-pills flex-column gap-1 mb-auto">
          @forelse($folders as $f)
            @php
              $isActive = (strtolower($f['name']) === strtolower($currentFolder));
            @endphp
            <li class="nav-item">
              <a href="{{ route('emails.index', ['folder' => $f['name']]) }}" 
                 class="nav-link py-2 px-2.5 rounded-3 d-flex align-items-center justify-content-between {{ $isActive ? 'active bg-warning text-dark fw-bold shadow-xs' : 'text-light' }}"
                 style="{{ !$isActive ? 'color: rgba(255,255,255,0.75);' : '' }}">
                <span class="d-flex align-items-center gap-2 text-truncate">
                  <i class="bi {{ $f['icon'] }} fs-6"></i>
                  <span class="d-none d-md-inline text-truncate">{{ $f['display_name'] }}</span>
                </span>
                @if($f['unseen'] > 0)
                  <span class="badge rounded-pill {{ $isActive ? 'bg-dark text-warning' : 'bg-warning text-dark' }} font-monospace" style="font-size: 10px;">
                    {{ $f['unseen'] }}
                  </span>
                @endif
              </a>
            </li>
          @empty
            <li class="nav-item">
              <a href="{{ route('emails.index', ['folder' => 'INBOX']) }}" class="nav-link active bg-warning text-dark fw-bold rounded-3">
                <i class="bi bi-inbox-fill me-2"></i>Inbox
              </a>
            </li>
          @endforelse
        </ul>

        <div class="pt-3 mt-3 border-top border-secondary border-opacity-25 small text-secondary d-none d-md-block" style="font-size: 11px;">
          <div><i class="bi bi-shield-lock-fill text-success me-1"></i>SSL Encrypted</div>
          <div class="text-truncate text-muted mt-0.5">imap.hostinger.com:993</div>
        </div>
      </div>

      <!-- 📨 Center / List View: Email Messages Table -->
      <div class="col col-md-4 col-xl-4 border-end border-secondary border-opacity-25 d-flex flex-column" id="emailListColumn">
        <!-- Search & Control Header -->
        <div class="p-3 border-bottom border-secondary border-opacity-25" style="background: rgba(0,0,0,0.06);">
          <form method="GET" action="{{ route('emails.index') }}" class="mb-2">
            <input type="hidden" name="folder" value="{{ $currentFolder }}">
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-dark border-secondary border-opacity-25 text-secondary"><i class="bi bi-search"></i></span>
              <input type="text" name="q" value="{{ $searchQuery }}" class="form-control bg-dark text-white border-secondary border-opacity-25" placeholder="Search sender, subject...">
              @if(!empty($searchQuery))
                <a href="{{ route('emails.index', ['folder' => $currentFolder]) }}" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
              @endif
              <button class="btn btn-outline-warning" type="submit">Search</button>
            </div>
          </form>

          <div class="d-flex justify-content-between align-items-center small text-secondary">
            <span class="fw-semibold text-capitalize text-warning">{{ $currentFolder }} &bull; {{ $total }} messages</span>
            <span style="font-size: 11px;">Page {{ $currentPage }} of {{ $totalPages }}</span>
          </div>
        </div>

        <!-- Emails Scrollable List -->
        <div class="flex-grow-1 overflow-y-auto" style="max-height: calc(100vh - 280px);">
          @forelse($messages as $msg)
            <div class="email-item-row p-3 border-bottom border-secondary border-opacity-15 cursor-pointer transition-all {{ !$msg['is_seen'] ? 'bg-warning bg-opacity-10 fw-bold border-start border-warning border-3' : '' }}" 
                 id="email-item-{{ $msg['id'] }}" 
                 onclick="loadEmailDetails('{{ urlencode($currentFolder) }}', {{ $msg['id'] }})"
                 style="cursor: pointer;">
              <div class="d-flex justify-content-between align-items-start mb-1">
                <div class="d-flex align-items-center gap-1.5 text-truncate me-2">
                  @if(!$msg['is_seen'])
                    <span class="badge bg-warning text-dark p-1 rounded-circle" style="width: 7px; height: 7px;"></span>
                  @endif
                  <span class="text-truncate text-white {{ !$msg['is_seen'] ? 'fw-bold' : 'fw-normal text-opacity-75' }}" style="font-size: 13px;">
                    {{ $msg['from_name'] ?: $msg['from_email'] }}
                  </span>
                </div>
                <small class="text-secondary font-monospace text-nowrap" style="font-size: 11px;">
                  {{ $msg['date_formatted'] }}
                </small>
              </div>

              <div class="text-truncate text-white small mb-1" style="font-size: 12.5px;">
                {{ $msg['subject'] }}
              </div>

              <div class="d-flex align-items-center justify-content-between">
                <small class="text-secondary text-truncate" style="font-size: 11px; max-width: 80%;">
                  {{ $msg['from_email'] }}
                </small>
                <div class="d-flex align-items-center gap-1.5">
                  @if($msg['has_attachments'])
                    <i class="bi bi-paperclip text-warning small" title="Contains Attachment"></i>
                  @endif
                  <button type="button" class="btn btn-link p-0 text-secondary email-star-btn" onclick="toggleEmailStar(event, '{{ urlencode($currentFolder) }}', {{ $msg['id'] }}, this)" title="Star">
                    <i class="bi {{ $msg['is_starred'] ? 'bi-star-fill text-warning' : 'bi-star' }}" style="font-size: 12px;"></i>
                  </button>
                </div>
              </div>
            </div>
          @empty
            <div class="text-center py-5 text-secondary">
              <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
              <div class="fw-semibold">No emails found</div>
              <small>This folder is currently empty.</small>
            </div>
          @endforelse
        </div>

        <!-- Pagination Footer -->
        @if($totalPages > 1)
          <div class="p-2 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-center bg-dark bg-opacity-25">
            @if($currentPage > 1)
              <a href="{{ route('emails.index', ['folder' => $currentFolder, 'page' => $currentPage - 1, 'q' => $searchQuery]) }}" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size: 11px;">
                <i class="bi bi-chevron-left me-1"></i>Prev
              </a>
            @else
              <div></div>
            @endif

            <span class="text-secondary small font-monospace" style="font-size: 11px;">{{ $currentPage }} / {{ $totalPages }}</span>

            @if($currentPage < $totalPages)
              <a href="{{ route('emails.index', ['folder' => $currentFolder, 'page' => $currentPage + 1, 'q' => $searchQuery]) }}" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size: 11px;">
                Next<i class="bi bi-chevron-right ms-1"></i>
              </a>
            @else
              <div></div>
            @endif
          </div>
        @endif
      </div>

      <!-- 📖 Right Pane: Active Email Reader -->
      <div class="col col-md-5 col-xl-6 d-flex flex-column position-relative" id="emailDetailPane" style="background: rgba(0,0,0,0.03);">
        
        <!-- Placeholder / Empty State -->
        <div id="emailDetailPlaceholder" class="m-auto text-center py-5 text-secondary">
          <div class="p-3 bg-dark bg-opacity-25 rounded-circle d-inline-block mb-3">
            <i class="bi bi-envelope-open text-warning fs-1"></i>
          </div>
          <h5 class="fw-bold text-white mb-1">Select an email to read</h5>
          <p class="text-secondary small">Click on any message from the list to view its complete content, sender details, and attachments.</p>
        </div>

        <!-- Loading Spinner -->
        <div id="emailDetailLoading" class="m-auto text-center py-5 d-none">
          <div class="spinner-border text-warning" role="status"></div>
          <div class="small text-secondary mt-2">Loading email securely from Hostinger...</div>
        </div>

        <!-- Active Email View Container -->
        <div id="emailDetailContent" class="d-none d-flex flex-column h-100">
          
          <!-- Reader Action Toolbar -->
          <div class="p-3 border-bottom border-secondary border-opacity-25 d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: rgba(0,0,0,0.08);">
            <div class="d-flex align-items-center gap-1.5">
              <button type="button" class="btn btn-sm btn-outline-warning d-flex align-items-center gap-1" onclick="replyCurrentEmail(false)">
                <i class="bi bi-reply-fill"></i><span class="d-none d-sm-inline">Reply</span>
              </button>
              <button type="button" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" onclick="replyCurrentEmail(true)">
                <i class="bi bi-reply-all-fill"></i><span class="d-none d-sm-inline">Reply All</span>
              </button>
              <button type="button" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" onclick="forwardCurrentEmail()">
                <i class="bi bi-forward-fill"></i><span class="d-none d-sm-inline">Forward</span>
              </button>
            </div>

            <div class="d-flex align-items-center gap-1.5">
              <button type="button" class="btn btn-sm btn-outline-secondary" id="btnToggleReadStatus" onclick="toggleCurrentReadStatus()" title="Mark as Unread">
                <i class="bi bi-envelope"></i>
              </button>
              <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteCurrentEmail()" title="Delete Email">
                <i class="bi bi-trash"></i>
              </button>
            </div>
          </div>

          <!-- Email Headers & Subject -->
          <div class="p-4 border-bottom border-secondary border-opacity-25">
            <h5 class="fw-bold text-white mb-3" id="viewEmailSubject">--</h5>
            
            <div class="d-flex align-items-start justify-content-between gap-3">
              <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning text-dark fw-bold d-flex align-items-center justify-content-center flex-shrink-0" id="viewSenderAvatar" style="width: 44px; height: 44px; font-size: 16px;">
                  CC
                </div>
                <div>
                  <div class="fw-bold text-white" id="viewSenderName">--</div>
                  <div class="text-secondary small">
                    From: <span class="text-warning font-monospace" id="viewSenderEmail">--</span>
                  </div>
                  <div class="text-secondary small" style="font-size: 11px;">
                    To: <span class="text-white-50" id="viewRecipientTo">--</span>
                  </div>
                </div>
              </div>

              <div class="text-end text-secondary small font-monospace" id="viewEmailDate">
                --
              </div>
            </div>
          </div>

          <!-- Attachments List (If Any) -->
          <div id="viewAttachmentsContainer" class="p-3 border-bottom border-secondary border-opacity-25 bg-dark bg-opacity-25 d-none">
            <div class="d-flex align-items-center gap-2 mb-2 text-warning small fw-bold">
              <i class="bi bi-paperclip"></i>
              <span>Attachments (<span id="viewAttachmentsCount">0</span>)</span>
            </div>
            <div class="d-flex flex-wrap gap-2" id="viewAttachmentsList">
              <!-- Dynamically populated chips -->
            </div>
          </div>

          <!-- Email HTML Content (Sandboxed) -->
          <div class="flex-grow-1 p-3 overflow-hidden d-flex flex-column">
            <iframe id="viewEmailIframe" 
                    class="w-100 flex-grow-1 border-0 rounded-3" 
                    sandbox="allow-same-origin allow-popups" 
                    style="background: #ffffff; min-height: 400px;"></iframe>
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
    <div class="modal-content border-0 shadow-lg rounded-4 text-white" style="background-color: var(--cc-bg-card, #151d2c);">
      <div class="modal-header border-bottom border-secondary border-opacity-25 py-3">
        <h5 class="modal-title fw-bold text-white d-flex align-items-center gap-2" id="composeEmailModalLabel">
          <i class="bi bi-pencil-square text-warning"></i>
          <span>New Message</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="composeEmailForm" onsubmit="handleSendEmail(event)" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="in_reply_to" id="composeInReplyTo">
        <input type="hidden" name="references" id="composeReferences">

        <div class="modal-body p-4 d-flex flex-column gap-3">
          
          <!-- From Badge -->
          <div class="d-flex align-items-center justify-content-between p-2 rounded-2 bg-dark bg-opacity-50 small border border-secondary border-opacity-25">
            <span class="text-secondary">Sending From:</span>
            <span class="text-warning fw-bold font-monospace">{{ $mailboxUsername }} (Hostinger SMTP)</span>
          </div>

          <!-- To Input -->
          <div>
            <label class="form-label small fw-bold text-secondary mb-1">To <span class="text-danger">*</span></label>
            <input type="text" name="to" id="composeTo" class="form-control bg-dark text-white border-secondary border-opacity-25" placeholder="recipient@example.com, another@example.com" required>
          </div>

          <!-- CC & BCC Toggles -->
          <div class="d-flex justify-content-end gap-2" style="font-size: 11px;">
            <a href="javascript:void(0)" class="text-secondary text-decoration-none" onclick="document.getElementById('composeCcWrapper').classList.toggle('d-none')">+ CC</a>
            <a href="javascript:void(0)" class="text-secondary text-decoration-none" onclick="document.getElementById('composeBccWrapper').classList.toggle('d-none')">+ BCC</a>
          </div>

          <!-- CC Input (Hidden by default) -->
          <div id="composeCcWrapper" class="d-none">
            <label class="form-label small fw-bold text-secondary mb-1">CC</label>
            <input type="text" name="cc" id="composeCc" class="form-control form-control-sm bg-dark text-white border-secondary border-opacity-25" placeholder="cc@example.com">
          </div>

          <!-- BCC Input (Hidden by default) -->
          <div id="composeBccWrapper" class="d-none">
            <label class="form-label small fw-bold text-secondary mb-1">BCC</label>
            <input type="text" name="bcc" id="composeBcc" class="form-control form-control-sm bg-dark text-white border-secondary border-opacity-25" placeholder="bcc@example.com">
          </div>

          <!-- Subject -->
          <div>
            <label class="form-label small fw-bold text-secondary mb-1">Subject <span class="text-danger">*</span></label>
            <input type="text" name="subject" id="composeSubject" class="form-control bg-dark text-white border-secondary border-opacity-25" placeholder="Enter email subject" required>
          </div>

          <!-- Message Body -->
          <div>
            <label class="form-label small fw-bold text-secondary mb-1">Message Body <span class="text-danger">*</span></label>
            <textarea name="body" id="composeBody" rows="9" class="form-control bg-dark text-white border-secondary border-opacity-25 font-monospace" placeholder="Type your message here..." style="font-size: 13px; line-height: 1.5;" required></textarea>
          </div>

          <!-- Attachments File Input -->
          <div>
            <label class="form-label small fw-bold text-secondary mb-1"><i class="bi bi-paperclip me-1"></i>Attach Files (Max 20MB)</label>
            <input type="file" name="attachments[]" id="composeAttachments" class="form-control form-control-sm bg-dark text-white border-secondary border-opacity-25" multiple>
          </div>

        </div>

        <div class="modal-footer border-top border-secondary border-opacity-25 py-3">
          <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" id="btnSubmitSendEmail" class="btn btn-warning btn-sm px-4 fw-bold d-flex align-items-center gap-1.5">
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

  // Highlight active row
  document.querySelectorAll('.email-item-row').forEach(row => row.classList.remove('active-email-row'));
  const rowEl = document.getElementById(`email-item-${id}`);
  if (rowEl) {
    rowEl.classList.add('active-email-row');
    rowEl.classList.remove('fw-bold');
    const dot = rowEl.querySelector('.rounded-circle');
    if (dot) dot.remove();
  }

  try {
    const url = `/admin/emails/${encodeURIComponent(folder)}/${id}`;
    const res = await fetch(url, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });

    if (!res.ok) throw new Error('Failed to load email from server');
    const data = await res.json();
    if (!data.success) throw new Error(data.error || 'Unable to parse email');

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
        chip.href = `/admin/emails/${encodeURIComponent(folder)}/${id}/attachment/${att.index}`;
        chip.className = 'btn btn-sm btn-dark border border-secondary border-opacity-25 d-flex align-items-center gap-1.5 text-white text-decoration-none py-1 px-2.5 rounded-2';
        chip.innerHTML = `
          <i class="bi bi-file-earmark-arrow-down text-warning"></i>
          <span class="text-truncate" style="max-width: 180px;">${escapeHtml(att.filename)}</span>
          <small class="text-secondary font-monospace">(${formatBytes(att.size)})</small>
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

<style>
.active-email-row {
  background: rgba(230, 176, 74, 0.15) !important;
  border-left: 4px solid #E6B04A !important;
}
.email-item-row:hover {
  background: rgba(255, 255, 255, 0.05);
}
</style>
@endpush
