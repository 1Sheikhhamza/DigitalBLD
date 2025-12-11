<div class="col-12 calendar-container-wrapper">
    <div class="calendar-container">
        <div class="calendar-main flex-grow-1 mb-4">
            <div class="calendar-header">
                <button id="prevMonthBtn" class="btn btn-outline-secondary"><i class="bi bi-chevron-left"></i></button>
                <h2 id="currentMonthYear"></h2>
                <button id="nextMonthBtn" class="btn btn-outline-secondary"><i class="bi bi-chevron-right"></i></button>
            </div>

            <div class="calendar-grid-header">
                <div>Sun</div>
                <div>Mon</div>
                <div>Tue</div>
                <div>Wed</div>
                <div>Thu</div>
                <div>Fri</div>
                <div>Sat</div>
            </div>

            <div id="calendarGrid" class="calendar-grid">
            </div>
        </div>

        <!-- Event List Section -->
        <div class="event-list-section flex-grow-1">
            <div class="event-list-header">
                <div class="row">
                    <div class="col-6">
                        <h3 id="eventsForDate">Events for <span class="text-primary">Today</span></h3>
                    </div>
                    <div class="col-6 text-end">
                        <button class="btn btn-primary btn-sm rounded-pill px-3 add-event-btn p-0" data-bs-toggle="modal" data-bs-target="#addEventModal" id="addEventBtn">
                            <i class="bi bi-plus-lg me-1"></i>Add Event
                        </button>
                    </div>
                </div>



            </div>
            <div id="eventList">
                <!-- Events will be dynamically loaded here -->
                <p class="text-muted text-center py-4" id="noEventsMessage">No events for this date.</p>
            </div>
        </div>
    </div>
</div>

<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEventModalLabel">Add New Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="eventForm">
                    <div class="mb-3">
                        <label for="eventTitle" class="form-label">Event Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-pill" id="eventTitle" required>
                    </div>

                    <div class="mb-3">
                        <label for="startDate" class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control rounded-pill" id="startDate" required>
                    </div>

                    <!-- <div class="mb-3">
                        <label for="endDate" class="form-label">End Date (Optional)</label>
                        <input type="date" class="form-control rounded-pill" id="endDate">
                    </div> -->

                    <div class="mb-3">
                        <label for="startTime" class="form-label">Time</label>
                        <input type="time" class="form-control rounded-pill" id="startTime">
                    </div>

                    <!-- <div class="mb-3">
                        <label for="endTime" class="form-label">End Time (Optional)</label>
                        <input type="time" class="form-control rounded-pill" id="endTime">
                    </div> -->

                    <!-- <div class="mb-3">
                        <label for="color" class="form-label">Event Color (Optional)</label>
                        <input type="color" class="form-control form-control-color rounded-pill" id="color" value="#3788d8" title="Choose event color">
                    </div> -->

                    <div class="mb-3">
                        <label for="eventDescription" class="form-label">Description (Optional)</label>
                        <textarea class="form-control rounded-pill" id="eventDescription" rows="3"></textarea>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary rounded-pill">Save Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Generic Alert Modal (for messages like "Please enter event title") -->
<div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="alertModalLabel">Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="alertModalBody">
                <!-- Message will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary rounded-pill" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<script>
    // ─── Element references ────────────────────────────────────────
    const calendarGrid = document.getElementById('calendarGrid');
    const currentMonthYear = document.getElementById('currentMonthYear');
    const prevMonthBtn = document.getElementById('prevMonthBtn');
    const nextMonthBtn = document.getElementById('nextMonthBtn');
    const eventList = document.getElementById('eventList');
    const eventsForDate = document.getElementById('eventsForDate');
    const noEventsMessage = document.getElementById('noEventsMessage');
    const eventForm = document.getElementById('eventForm');
    const addEventBtn = document.getElementById('addEventBtn');
    const eventTitleInput = document.getElementById('eventTitle');
    const eventStartDateInput = document.getElementById('startDate');
    // const eventEndDateInput = document.getElementById('endDate');
    const eventStartTimeInput = document.getElementById('startTime');
    // const eventEndTimeInput = document.getElementById('endTime');
    // const eventColorInput = document.getElementById('color');
    const eventDescriptionInput = document.getElementById('eventDescription');

    // ─── State & Routes ───────────────────────────────────────────
    let currentDate = new Date();
    let selectedDate = new Date();
    let events = {};

    document.addEventListener('DOMContentLoaded', loadEvents);
    prevMonthBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 0);
        renderCalendar();
    });
    nextMonthBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 0);
        renderCalendar();
    });

    // **Reset and pre‑fill the Add Event modal on show**
    document.getElementById('addEventModal')
        .addEventListener('show.bs.modal', function() {
            // Clear all fields
            eventTitleInput.value = '';
            eventStartDateInput.value = getFormattedDate(selectedDate);
            // eventEndDateInput.value = '';
            eventStartTimeInput.value = '';
            // eventEndTimeInput.value = '';
            // eventColorInput.value = '#3788d8'; // or whatever default
            eventDescriptionInput.value = '';
        });

    const eventsIndexUrl = "{{ route('subscriber.events.json') }}";
    const eventsStoreUrl = "{{ route('subscriber.events.store') }}";

    function getEventDestroyUrl(id) {
        return "{{ route('subscriber.events.destroy', ':id') }}".replace(':id', id);
    }
    /* function getEventUpdateUrl(id) {
      return "{{ route('subscriber.events.update', ':id') }}".replace(':id', id);
    } */

    // ─── Utility ──────────────────────────────────────────────────
    function getFormattedDate(d) {
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        return `${y}-${m}-${dd}`;
    }

    function getMonthName(d) {
        return d.toLocaleString('en-US', {
            month: 'long'
        });
    }

    // ─── Calendar Rendering ────────────────────────────────────────
    function renderCalendar() {
        calendarGrid.innerHTML = '';
        const y = currentDate.getFullYear();
        const m = currentDate.getMonth();
        currentMonthYear.textContent = `${getMonthName(currentDate)} ${y}`;

        const firstOfMonth = new Date(y, m, 1);
        const daysInMonth = new Date(y, m + 1, 0).getDate();
        const startWeek = firstOfMonth.getDay();
        const prevMonthDays = new Date(y, m, 0).getDate();

        // prev-month
        for (let i = startWeek; i > 0; i--) {
            const d = prevMonthDays - i + 1;
            appendDay(d, 'prev-month', new Date(y, m - 1, d));
        }
        // this-month
        for (let d = 1; d <= daysInMonth; d++) {
            const dt = new Date(y, m, d);
            const cls = [
                dt.toDateString() === new Date().toDateString() ? 'current-day' : '',
                dt.toDateString() === selectedDate.toDateString() ? 'selected-day' : ''
            ].filter(c => c).join(' ');
            appendDay(d, cls, dt);
        }
        // next-month to fill grid
        const total = startWeek + daysInMonth;
        const fill = total <= 35 ? 35 - total : 42 - total;
        for (let i = 1; i <= fill; i++) {
            appendDay(i, 'next-month', new Date(y, m + 1, i));
        }
    }

    function appendDay(dayNum, classes, dateObj) {
        const div = document.createElement('div');
        div.classList.add('calendar-day', ...classes.split(' ').filter(Boolean));
        const key = getFormattedDate(dateObj);
        div.dataset.date = key;

        const span = document.createElement('span');
        span.classList.add('day-number');
        span.textContent = dayNum;
        div.appendChild(span);

        // dots
        if (events[key]?.length) {
            const dotC = document.createElement('div');
            dotC.classList.add('event-dot-container');
            const count = Math.min(events[key].length, 3);
            for (let i = 0; i < count; i++) {
                const dot = document.createElement('span');
                dot.classList.add('event-dot');
                dotC.appendChild(dot);
            }
            div.appendChild(dotC);
        }

        if (!div.classList.contains('prev-month') && !div.classList.contains('next-month')) {
            div.addEventListener('click', () => selectDay(div));
        }
        calendarGrid.appendChild(div);
    }

    function selectDay(div) {
        document.querySelector('.calendar-day.selected-day')?.classList.remove('selected-day');
        div.classList.add('selected-day');
        selectedDate = new Date(div.dataset.date);
        displayEventsForSelectedDate();
    }

    // ─── Display Events ───────────────────────────────────────────
    function displayEventsForSelectedDate() {
        eventList.innerHTML = '';
        const key = getFormattedDate(selectedDate);
        const list = events[key] || [];

        eventsForDate.innerHTML =
            `Events for <span class="text-primary">` +
            selectedDate.toLocaleDateString('en-US', {
                weekday: 'short',
                month: 'short',
                day: 'numeric'
            }) +
            `</span>`;

        addEventBtn.dataset.bsToggle = 'modal';
        addEventBtn.dataset.bsTarget = '#addEventModal';

        if (!list.length) {
            noEventsMessage.style.display = 'block';
            eventList.appendChild(noEventsMessage);
            return;
        }
        noEventsMessage.style.display = 'none';

        list.forEach(ev => {
            const item = document.createElement('div');
            item.classList.add('col-sm-12');
            // item.style.borderLeft = `6px solid ${ev.color || '#0d6efd'}`;
            item.style.borderLeft = '#0d6efd';

            const startDate = ev.start_date ? new Date(ev.start_date).toLocaleDateString() : '';
            // const endDate = ev.end_date ? new Date(ev.end_date).toLocaleDateString() : '';
            // const dateRange = endDate && startDate !== endDate ? `${startDate} → ${endDate}` : startDate;
            /* const timeRange = ev.start_time && ev.end_time ?
                `${ev.start_time} → ${ev.end_time}` :
                ev.start_time ?
                `${ev.start_time}` :
                ''; */
            const dateRange = startDate;
            const timeRange = ev.start_time;

            item.innerHTML = `
                <div class="d-flex py-3 justify-content-start align-items-center border-bottom">
                    <div class="me-3" style="min-width: 80px; font-weight: 600; color: #666">
                        ${ev.start_time || ''}
                    </div>
                    <div>${ev.title || 'Untitled Event'}</div>
                    </div>`;


            eventList.appendChild(item);
        });

        document.querySelectorAll('.delete-event-btn').forEach(btn => {
            btn.addEventListener('click', e => {
                e.stopPropagation();
                deleteEvent(btn.dataset.id);
            });
        });
    }


    // ─── CRUD via AJAX ────────────────────────────────────────────
    async function loadEvents() {
        try {
            const res = await fetch(eventsIndexUrl, {
                headers: {
                    'Accept': 'application/json'
                }
            });
            const text = await res.text();
            if (!res.ok) throw new Error(text);
            const data = JSON.parse(text);
            events = {};
            data.forEach(ev => {
                const key = ev.start_date;
                events[key] = events[key] || [];
                events[key].push(ev);
            });
            renderCalendar();
            // select today on first load
            const todayKey = getFormattedDate(new Date());
            const todayDiv = document.querySelector(`.calendar-day[data-date="${todayKey}"]`);
            if (todayDiv) selectDay(todayDiv);
            else displayEventsForSelectedDate();
        } catch (err) {
            console.error('loadEvents failed:', err);
            const alert = new bootstrap.Modal(document.getElementById('alertModal'));
            document.getElementById('alertModalBody').textContent = 'Unable to load events';
            alert.show();
        } finally {
            renderCalendar(); // ✅ Always render calendar
            const todayKey = getFormattedDate(new Date());
            const todayDiv = document.querySelector(`.calendar-day[data-date="${todayKey}"]`);
            if (todayDiv) selectDay(todayDiv);
            else displayEventsForSelectedDate();
        }
    }

    async function deleteEvent(id) {
        const res = await fetch(getEventDestroyUrl(id), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        });

        if (res.ok) {
            await loadEvents(); // Reload all events from DB
            renderCalendar(); // Refresh the calendar view
            displayEventsForSelectedDate(); // Refresh events list for selected date
        } else {
            const alertModal = new bootstrap.Modal(document.getElementById('alertModal'));
            document.getElementById('alertModalBody').textContent = 'Failed to delete event.';
            alertModal.show();
        }
    }



    eventForm.addEventListener('submit', async e => {
        e.preventDefault();
        const title = eventTitleInput.value.trim();
        const start_date = eventStartDateInput.value;
        if (!title || !start_date) {
            const alert = new bootstrap.Modal(document.getElementById('alertModal'));
            document.getElementById('alertModalBody').textContent = 'Title & start date required';
            return alert.show();
        }
        const payload = {
            title,
            description: eventDescriptionInput.value.trim() || null,
            start_date,
            // end_date: eventEndDateInput.value || null,
            start_time: eventStartTimeInput.value || null,
            // end_time: eventEndTimeInput.value || null,
            // color: eventColorInput.value || null,
        };
        try {
            const res = await fetch(eventsStoreUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(payload)
            });
            const text = await res.text();
            if (!res.ok) throw new Error(text);
            await loadEvents();
            const modalEl = document.getElementById('addEventModal');
            bootstrap.Modal.getInstance(modalEl).hide();
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            eventForm.reset();
        } catch (err) {
            console.error('save failed:', err);
            const alert = new bootstrap.Modal(document.getElementById('alertModal'));
            document.getElementById('alertModalBody').textContent = 'Could not save event';
            alert.show();
        }
    });

    // ─── Initialization ───────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', loadEvents);
    prevMonthBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    });
    nextMonthBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    });
</script>