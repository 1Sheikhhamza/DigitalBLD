<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>Digital BLD - Subscriber Dashboard</title>
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
  <link rel="stylesheet" href="{{ asset('frontend/assets/vendor/bootstrap/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('frontend/assets/css/custom/custom.css') }}">
  <link rel="stylesheet" href="{{ asset('frontend/assets/css/custom/dashboard.css') }}">
  <link rel="stylesheet" href="{{ asset('frontend/assets/css/custom/calender.css') }}">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="{{asset('assets/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="{{ asset('assets/css/fonts.css') }}">
  <link rel="shortcut icon" href="{{ url('assets/img/favicon.png') }}">
  <link rel="icon" type="image/png" href="{{ url('assets/img/favicon.png') }}" sizes="192x192">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ url('assets/img/favicon.png') }}">
  @stack('styles')
  <style>
    @media print {
      @page {
        size: A4;
        margin: 1.5cm;
      }

      .navbar,
      .announcement-bar,
      #chat-floating-btn,
      #chat-widget,
      .print-btn-container,
      footer,
      .footer {
        display: none !important;
      }

      body {
        background: white;
        color: black;
        font-size: 11pt;
        /* Slightly smaller for better fit */
      }

      a {
        text-decoration: none;
        color: black;
      }

      .container {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
      }

      .list-group-item {
        border: none;
        border-bottom: 1px solid #ddd;
        /* Cleaner look */
        page-break-inside: avoid;
        break-inside: avoid;
      }

      h4 {
        margin-top: 0;
        padding-top: 10px;
      }
    }
  </style>
</head>

<body>

  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand logo d-flex align-items-center" href="{{ route('home') }}">
        <img src="{{ asset('frontend/assets/img/logo.png') }}">
        <span class="sitename">Digital BLD</span>
      </a>

      @php
        $today = now()->format('d F, Y');
        $isDashboard = Route::currentRouteName() === 'subscriber.dashboard';
      @endphp

      <div class="d-flex align-items-center ms-auto">
        @if($isDashboard)
          <span class="navbar-text me-3 d-none d-md-inline">
            @if (!empty($hasAnySubscription?->activeSubscription?->package?->name))
              Package <br />
              <span class="fw-bold">{{ $hasAnySubscription->activeSubscription->package->name }}</span> |
            @endif
            {{ $today }}
          </span>


          <!-- Reminder Dropdown -->
          <div class="dropdown d-inline-block position-relative mx-2">
            <a href="#" class="text-dark position-relative" id="reminderDropdown" role="button" data-bs-toggle="dropdown"
              aria-expanded="false">
              <i class="bi bi-bell fs-5"></i>
              @if(isset($activeReminders) && $activeReminders->count() > 0)
                <span
                  class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                  <span class="visually-hidden">New alerts</span>
                </span>
              @endif
            </a>
            <ul class="dropdown-menu dropdown-menu-end p-0 shadow border-0" aria-labelledby="reminderDropdown"
              style="width: 300px;">
              <li class="p-2 border-bottom fw-bold bg-light rounded-top">Reminders</li>
              @if(isset($activeReminders) && $activeReminders->count() > 0)
                @foreach($activeReminders as $reminder)
                  <li>
                    <a class="dropdown-item p-2 text-wrap" href="#" onclick="markReminderRead({{$reminder->id}}, event)">
                      <div class="d-flex align-items-start">
                        <i class="bi bi-calendar-event text-primary me-2 mt-1"></i>
                        <div>
                          <div class="fw-bold small">{{ $reminder->title }}</div>
                          <div class="text-muted small" style="font-size: 0.8em;">
                            @if($reminder->start_time)
                              {{ \Carbon\Carbon::parse($reminder->start_time)->format('h:i A') }}
                            @else
                              All Day
                            @endif
                          </div>
                        </div>
                      </div>
                    </a>
                  </li>
                @endforeach
                <li>
                  <hr class="dropdown-divider m-0">
                </li>
                <li><a class="dropdown-item text-center small text-muted py-2" href="#"
                    onclick="markReminderRead(null, event)">Mark all as read</a></li>
              @else
                <li class="p-3 text-center text-muted small">No active reminders</li>
              @endif
            </ul>
          </div>
        @else
          @include('auth.subscribers.layouts._nav')
        @endif

        @auth('subscriber')
          @include('auth.subscribers.layouts._profile')
        @endauth
      </div>

    </div>

  </nav>

  @if(!Route::is('subscriber.myDecision.editNote') && !Route::is('ai.research') && ((isset($announcements) && $announcements->count() > 0) || (isset($activeReminders) && $activeReminders->count() > 0)))
    <div class="announcement-bar footer dark-background py-2 overflow-hidden"
      style="border-top: none; padding-top: 10px; padding-bottom: 10px; background: linear-gradient(90deg, #003092, #0051c4);">
      <div class="container">
        <div class="d-flex align-items-center">
          <div
            class="announcement-label bg-danger text-white px-2 py-1 me-3 rounded small fw-bold text-nowrap d-flex align-items-center justify-content-center"
            style="margin-left: -15px;">
            Announcements
          </div>
          <div class="marquee-container w-100 overflow-hidden position-relative">
            <div class="marquee-content d-inline-block text-nowrap">
              @foreach($announcements as $announcement)
                <span class="me-5">
                  <i class="bi bi-megaphone-fill me-2 text-warning"></i>
                  {{ $announcement->message }}
                </span>
              @endforeach

              @if(isset($activeReminders) && $activeReminders->count() > 0)
                @foreach($activeReminders as $reminder)
                  <span class="me-5">
                    <i class="bi bi-alarm-fill me-2 text-danger"></i>
                    <span class="fw-bold text-danger">Reminder:</span> {{ $reminder->title }}
                    @if($reminder->start_time)
                      - {{ \Carbon\Carbon::parse($reminder->start_time)->format('h:i A') }}
                    @endif
                  </span>
                @endforeach
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>

    <style>
      .marquee-content {
        display: inline-block;
        animation: marquee 40s linear infinite;
        font-size: 1.1em;
        color: #fff;
      }

      .marquee-container:hover .marquee-content {
        animation-play-state: paused;
      }

      @keyframes marquee {
        0% {
          transform: translateX(100%);
        }

        100% {
          transform: translateX(-100%);
        }
      }
    </style>
  @endif

  @if(!Route::is('subscriber.myDecision.editNote') && !Route::is('ai.research'))
    <!-- Chat Widget -->
    <!-- Floating Chat Button -->
    <div id="chat-floating-btn" onclick="toggleChat()"
      style="position: fixed; bottom: 30px; left: 30px; width: 60px; height: 60px; background-color: #0d6efd; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.3); z-index: 1060; transition: transform 0.2s;">
      <i class="bi bi-chat-dots-fill fs-3"></i>
    </div>

    <div id="chat-widget" class="card shadow border-0"
      style="display: none; position: fixed; bottom: 100px; left: 30px; width: 350px; z-index: 1050; transition: all 0.3s ease;">
      <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <span class="fw-bold"><i class="bi bi-chat-dots"></i> Support Chat</span>
        <div>
          <button class="btn btn-sm btn-link text-white p-0 me-2" onclick="maximizeChat()">
            <i class="bi bi-arrows-fullscreen" id="maximize-icon"></i>
          </button>
          <button class="btn btn-sm btn-link text-white p-0 me-2" onclick="minimizeChat()"><i
              class="bi bi-dash-lg"></i></button>
          <button class="btn btn-sm btn-link text-white p-0" onclick="toggleChat()"><i class="bi bi-x-lg"></i></button>
        </div>
      </div>
      <div class="card-body p-0" id="chat-body">
        <div id="chat-messages" class="p-3 bg-light" style="height: 300px; overflow-y: auto;">
          <div class="text-center text-muted small mt-5">Loading messages...</div>
        </div>
        <div class="p-2 border-top bg-white">
          <form id="chat-form" onsubmit="sendMessage(event)">
            <div class="input-group">
              <input type="text" id="chat-input" class="form-control form-control-sm" placeholder="Type your question..."
                required>
              <button class="btn btn-primary btn-sm" type="submit"><i class="bi bi-send"></i></button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <style>
      .message-bubble {
        max-width: 80%;
        padding: 8px 12px;
        border-radius: 12px;
        margin-bottom: 8px;
        font-size: 0.9rem;
      }

      .message-sent {
        background-color: #0d6efd;
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 2px;
        margin-left: auto;
      }

      .message-received {
        background-color: #e9ecef;
        color: black;
        align-self: flex-start;
        border-bottom-left-radius: 2px;
      }
    </style>

    <script>
      let chatOpen = false;
      let chatMinimized = false;
      let chatMaximized = false;
      let chatPollInterval = null;

      function toggleChat() {
        const widget = document.getElementById('chat-widget');
        const btn = document.getElementById('chat-floating-btn');

        if (chatOpen) {
          widget.style.display = 'none';
          btn.style.display = 'flex'; // Show button when chat closed
          clearInterval(chatPollInterval);
          chatOpen = false;
        } else {
          widget.style.display = 'block';
          btn.style.display = 'none'; // Hide button when chat open
          chatOpen = true;
          chatMinimized = false;
          document.getElementById('chat-body').style.display = 'block';
          fetchMessages();
          chatPollInterval = setInterval(fetchMessages, 3000);
        }
      }

      function maximizeChat() {
        const widget = document.getElementById('chat-widget');
        const messages = document.getElementById('chat-messages');
        const icon = document.getElementById('maximize-icon');

        if (chatMaximized) {
          // Restore
          widget.style.width = '350px';
          widget.style.height = 'auto'; // reset height
          widget.style.bottom = '100px';
          widget.style.right = 'auto'; // clear right
          widget.style.left = '30px';  // set left
          widget.style.top = 'auto';
          messages.style.height = '300px';
          icon.classList.remove('bi-fullscreen-exit');
          icon.classList.add('bi-arrows-fullscreen');
          chatMaximized = false;
        } else {
          // Maximize
          widget.style.width = '90vw';
          widget.style.height = '80vh';
          widget.style.bottom = '10vh';
          widget.style.right = '5vw';
          widget.style.top = '10vh';
          widget.style.left = '5vw';
          messages.style.height = 'calc(80vh - 105px)'; // Adjust for header and input
          icon.classList.remove('bi-arrows-fullscreen');
          icon.classList.add('bi-fullscreen-exit');
          chatMaximized = true;
          chatMinimized = false;
          document.getElementById('chat-body').style.display = 'block';
        }
      }

      // Draggable Logic
      const floatingBtn = document.getElementById('chat-floating-btn');
      let isDragging = false;
      let currentX;
      let currentY;
      let initialX;
      let initialY;
      let xOffset = 0;
      let yOffset = 0;

      floatingBtn.addEventListener("mousedown", dragStart);
      floatingBtn.addEventListener("mouseup", dragEnd);
      floatingBtn.addEventListener("mousemove", drag);

      floatingBtn.addEventListener("touchstart", dragStart, { passive: false });
      floatingBtn.addEventListener("touchend", dragEnd);
      floatingBtn.addEventListener("touchmove", drag, { passive: false });

      function dragStart(e) {
        if (e.type === "touchstart") {
          initialX = e.touches[0].clientX - xOffset;
          initialY = e.touches[0].clientY - yOffset;
        } else {
          initialX = e.clientX - xOffset;
          initialY = e.clientY - yOffset;
        }

        if (e.target.closest('#chat-floating-btn')) {
          isDragging = true;
        }
      }

      function dragEnd(e) {
        initialX = currentX;
        initialY = currentY;
        isDragging = false;
      }

      function drag(e) {
        if (isDragging) {
          e.preventDefault();

          if (e.type === "touchmove") {
            currentX = e.touches[0].clientX - initialX;
            currentY = e.touches[0].clientY - initialY;
          } else {
            currentX = e.clientX - initialX;
            currentY = e.clientY - initialY;
          }

          xOffset = currentX;
          yOffset = currentY;

          setTranslate(currentX, currentY, floatingBtn);
        }
      }

      function setTranslate(xPos, yPos, el) {
        el.style.transform = "translate3d(" + xPos + "px, " + yPos + "px, 0)";
      }

      function minimizeChat() {
        const body = document.getElementById('chat-body');
        if (chatMinimized) {
          body.style.display = 'block';
          chatMinimized = false;
        } else {
          body.style.display = 'none';
          chatMinimized = true;
        }
      }

      function fetchMessages() {
        $.get("{{ route('subscriber.chat.messages') }}", function (messages) {
          // const container = document.getElementById('chat-messages');
          // container.innerHTML = ''; // This clears everything, making it hard to read if polling often. Ideally append only new.

          const container = document.getElementById('chat-messages');
          container.innerHTML = '';
          if (messages.length === 0) {
            container.innerHTML = '<div class="text-center text-muted small mt-5">No messages yet. Ask a question!</div>';
            return;
          }

          messages.forEach(msg => {
            const div = document.createElement('div');
            div.className = `message-bubble ${msg.sender_type === 'subscriber' ? 'message-sent' : 'message-received'}`;
            div.textContent = msg.message;
            container.appendChild(div);
          });

          if (!chatMinimized) container.scrollTop = container.scrollHeight;
        });
      }

      function sendMessage(e) {
        e.preventDefault();
        const input = document.getElementById('chat-input');
        const message = input.value;
        if (!message.trim()) return;

        $.ajax({
          url: "{{ route('subscriber.chat.send') }}",
          type: "POST",
          data: {
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            message: message
          },
          success: function (response) {
            if (response.success) {
              input.value = '';
              fetchMessages();
            } else {
              alert('Failed to send message: ' + (response.message || 'Unknown error'));
            }
          },
          error: function (xhr) {
            console.error(xhr);
            if (xhr.status === 419) {
              alert('Session expired. Please refresh the page.');
            } else {
              alert('Error sending message. Status: ' + xhr.status);
            }
          }
        });
      }
    </script>
  @endif
  <form id="logoutForm" action="{{ route('subscriber.logout') }}" method="POST" style="display:none;">
    @csrf
  </form>

  @yield('content')


  @include('layouts.footer')

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
  <script src="{{ asset('frontend/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{asset('assets/select2/select2.min.js')}}"></script>
  <script src="{{ asset('assets/ckeditor/ckeditor.js') }}"></script>
  <script>
    $('.select2Data').select2({
      placeholder: "Select a user",
      // no allowClear → no cross button
      dropdownParent: $('#shareModal'),
      width: '100%',
      minimumInputLength: 1,
      minimumResultsForSearch: 0,
      matcher: function (params, data) {
        if ($.trim(params.term) === '') {
          return null; // don’t show anything until user types
        }

        let text = data.text.split('(')[0].trim();
        let words = text.split(/\s+/);
        let term = params.term.toLowerCase();

        let isMatch = words.some(word => word.toLowerCase().startsWith(term));

        return isMatch ? data : null;
      }
    });


    $('.select2Data').not('#shareModal .select2Data').select2({
      // allowClear: true
    });
  </script>


  <script>
    // Disable right-click
    document.addEventListener('contextmenu', function (e) {
      e.preventDefault();
    });

    // Disable text selection
    document.addEventListener('selectstart', function (e) {
      e.preventDefault();
    });
    document.addEventListener('copy', function (e) {
      e.preventDefault();
    });

    // Disable drag
    document.addEventListener('dragstart', function (e) {
      e.preventDefault();
    });

    // Disable common keyboard shortcuts
    document.addEventListener('keydown', function (e) {
      // Block F12 (DevTools)
      if (e.key === "F12") {
        e.preventDefault();
      }

      // Block Ctrl+Shift+I or Cmd+Option+I
      if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key.toLowerCase() === 'i') {
        e.preventDefault();
      }

      // Block Ctrl+S or Cmd+S (Save page)
      if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
        e.preventDefault();
      }

      // Block Ctrl+U or Cmd+U (View source)
      if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'u') {
        e.preventDefault();
      }

      // Block Ctrl+C / Ctrl+X / Ctrl+V (Copy / Cut / Paste)
      if ((e.ctrlKey || e.metaKey) && ['c', 'x', 'v', 'a', 'p'].includes(e.key.toLowerCase())) {
        e.preventDefault();
      }
    });

    document.addEventListener('DOMContentLoaded', function () {
      document.body.style.userSelect = 'none';
      document.body.style.webkitUserSelect = 'none';
      document.body.style.msUserSelect = 'none';
    });
  </script>



  @stack('scripts')

  <script>
    function markReminderRead(id, e) {
      if (e) e.preventDefault();

      let data = {
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      };

      if (id) data.id = id;

      $.ajax({
        url: "{{ route('subscriber.reminders.markAsRead') }}",
        type: "POST",
        data: data,
        success: function (response) {
          if (response.success) {
            window.location.reload();
          }
        },
        error: function () {
          alert('Error updating reminder status.');
        }
      });
    }
  </script>
</body>

</html>