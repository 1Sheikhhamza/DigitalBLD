<div class="col-12 calendar-container-wrapper">
    <div class="calendar-container">
        <div class="calendar-main flex-grow-1 mb-4">
            <!-- Calendar Header -->
            <div class="calendar-header">
                <button id="prevMonthBtn" class="btn btn-outline-secondary"><i class="bi bi-chevron-left"></i></button>
                <h2 id="currentMonthYear"></h2>
                <button id="nextMonthBtn" class="btn btn-outline-secondary"><i class="bi bi-chevron-right"></i></button>
            </div>

            <!-- Weekday Headers -->
            <div class="calendar-grid-header">
                <div>Sun</div>
                <div>Mon</div>
                <div>Tue</div>
                <div>Wed</div>
                <div>Thu</div>
                <div>Fri</div>
                <div>Sat</div>
            </div>

            <!-- Calendar Grid -->
            <div id="calendarGrid" class="calendar-grid">
                <!-- Days will be dynamically generated here by JavaScript -->
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
            <i class="bi bi-plus-lg me-1"></i> Add Event
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
                        <label for="eventTitle" class="form-label">Event Title</label>
                        <input type="text" class="form-control rounded-pill" id="eventTitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="eventDate" class="form-label">Date</label>
                        <input type="date" class="form-control rounded-pill" id="eventDate" required>
                    </div>
                    <div class="mb-3">
                        <label for="eventTime" class="form-label">Time (Optional)</label>
                        <input type="time" class="form-control rounded-pill" id="eventTime">
                    </div>
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

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const calendarGrid = document.getElementById('calendarGrid');
    const currentMonthYear = document.getElementById('currentMonthYear');
    const prevMonthBtn = document.getElementById('prevMonthBtn');
    const nextMonthBtn = document.getElementById('nextMonthBtn');
    const eventList = document.getElementById('eventList');
    const eventsForDate = document.getElementById('eventsForDate');
    const noEventsMessage = document.getElementById('noEventsMessage');
    const eventForm = document.getElementById('eventForm');
    const eventTitleInput = document.getElementById('eventTitle');
    const eventDateInput = document.getElementById('eventDate');
    const eventTimeInput = document.getElementById('eventTime');
    const eventDescriptionInput = document.getElementById('eventDescription');
    const addEventBtn = document.getElementById('addEventBtn');

    let currentDate = new Date();
    let selectedDate = new Date(); // Stores the date clicked on the calendar

    // Store events in an object where keys are YYYY-MM-DD dates
    let events = JSON.parse(localStorage.getItem('calendarEvents')) || {};

    // --- Utility Functions ---

    function getFormattedDate(date) {
        const year = date.getFullYear();
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const day = date.getDate().toString().padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function getMonthName(date) {
        return date.toLocaleString('en-US', {
            month: 'long'
        });
    }

    function getUniqueId() {
        return Date.now().toString(36) + Math.random().toString(36).substr(2);
    }

    function saveEvents() {
        localStorage.setItem('calendarEvents', JSON.stringify(events));
    }

    // --- Calendar Rendering ---

    function renderCalendar() {
        calendarGrid.innerHTML = ''; // Clear previous days
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth(); // 0-indexed

        currentMonthYear.textContent = `${getMonthName(currentDate)} ${year}`;

        const firstDayOfMonth = new Date(year, month, 1);
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const firstDayOfWeek = firstDayOfMonth.getDay(); // 0 (Sunday) to 6 (Saturday)

        // Calculate days from previous month to show
        const prevMonthDays = new Date(year, month, 0).getDate();
        for (let i = firstDayOfWeek; i > 0; i--) {
            const day = prevMonthDays - i + 1;
            const prevMonthDate = new Date(year, month - 1, day);
            const dayDiv = createDayElement(day, 'prev-month', prevMonthDate);
            calendarGrid.appendChild(dayDiv);
        }

        // Days of the current month
        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month, day);
            const isCurrentDay = date.toDateString() === new Date().toDateString();
            const isSelectedDay = date.toDateString() === selectedDate.toDateString();
            let classes = [];
            if (isCurrentDay) classes.push('current-day');
            if (isSelectedDay) classes.push('selected-day');

            const dayDiv = createDayElement(day, classes.join(' '), date);
            calendarGrid.appendChild(dayDiv);
        }

        // Calculate days from next month to show (fill the grid)
        const totalDaysRendered = firstDayOfWeek + daysInMonth;
        // Ensure at least 6 rows for consistent layout
        const cellsToFill = totalDaysRendered <= 35 ? (35 - totalDaysRendered) : (42 - totalDaysRendered);

        for (let i = 1; i <= cellsToFill; i++) {
            const nextMonthDate = new Date(year, month + 1, i);
            const dayDiv = createDayElement(i, 'next-month', nextMonthDate);
            calendarGrid.appendChild(dayDiv);
        }
        displayEventsForSelectedDate(); // Refresh event list based on the new calendar view
    }

    function createDayElement(dayNum, classes, dateObj) {
        const dayDiv = document.createElement('div');
        // Filter out empty strings before adding to classList to prevent "Failed to execute 'add' on 'DOMTokenList'" error
        const classListToAdd = classes.split(' ').filter(cls => cls !== '');
        dayDiv.classList.add('calendar-day', ...classListToAdd);
        dayDiv.dataset.date = getFormattedDate(dateObj); // Store full date

        const dayNumberSpan = document.createElement('span');
        dayNumberSpan.classList.add('day-number');
        dayNumberSpan.textContent = dayNum;
        dayDiv.appendChild(dayNumberSpan);

        // Add event dots if there are events for this day
        const formattedDate = getFormattedDate(dateObj);
        if (events[formattedDate] && events[formattedDate].length > 0) {
            const dotContainer = document.createElement('div');
            dotContainer.classList.add('event-dot-container');
            // Limit dots to prevent clutter
            const numDots = Math.min(events[formattedDate].length, 3);
            for (let i = 0; i < numDots; i++) {
                const dot = document.createElement('span');
                dot.classList.add('event-dot');
                dotContainer.appendChild(dot);
            }
            dayDiv.appendChild(dotContainer);
        }

        // Add click listener only for current month days
        if (!dayDiv.classList.contains('prev-month') && !dayDiv.classList.contains('next-month')) {
            dayDiv.addEventListener('click', () => {
                selectDay(dayDiv);
            });
        }

        return dayDiv;
    }

    function selectDay(clickedDayDiv) {
        // Remove 'selected-day' from previously selected day
        const currentSelected = document.querySelector('.calendar-day.selected-day');
        if (currentSelected) {
            currentSelected.classList.remove('selected-day');
        }

        // Add 'selected-day' to the clicked day
        clickedDayDiv.classList.add('selected-day');
        selectedDate = new Date(clickedDayDiv.dataset.date); // Update selectedDate
        displayEventsForSelectedDate();
    }

    // --- Event Display ---

    function displayEventsForSelectedDate() {
        eventList.innerHTML = ''; // Clear previous events
        const formattedSelectedDate = getFormattedDate(selectedDate);
        const eventsOnSelectedDate = events[formattedSelectedDate] || [];

        eventsForDate.innerHTML = `Events for <span class="text-primary">${selectedDate.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' })}</span>`;
        addEventBtn.dataset.bsToggle = 'modal'; // Ensure add event button shows modal
        addEventBtn.dataset.bsTarget = '#addEventModal';

        if (eventsOnSelectedDate.length === 0) {
            noEventsMessage.style.display = 'block';
            eventList.appendChild(noEventsMessage);
        } else {
            noEventsMessage.style.display = 'none';
            eventsOnSelectedDate.forEach(event => {
                const eventItem = document.createElement('div');
                eventItem.classList.add('event-list-item');
                eventItem.dataset.id = event.id; // Store event ID for deletion

                eventItem.innerHTML = `
                        <h5>${event.title}</h5>
                        <p class="mb-0">${event.time ? event.time + ' - ' : ''}${event.description || 'No description'}</p>
                        <button type="button" class="delete-event-btn" data-id="${event.id}">
                            <i class="bi bi-trash"></i>
                        </button>
                    `;
                eventList.appendChild(eventItem);
            });
            addDeleteEventListeners(); // Attach listeners after creating items
        }
    }

    function addDeleteEventListeners() {
        document.querySelectorAll('.delete-event-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                e.stopPropagation(); // Prevent selecting the day when clicking delete
                const eventIdToDelete = button.dataset.id;
                deleteEvent(eventIdToDelete);
            });
        });
    }

    function deleteEvent(eventId) {
        const formattedSelectedDate = getFormattedDate(selectedDate);
        if (events[formattedSelectedDate]) {
            events[formattedSelectedDate] = events[formattedSelectedDate].filter(event => event.id !== eventId);
            if (events[formattedSelectedDate].length === 0) {
                delete events[formattedSelectedDate]; // Remove array if no events left
            }
            saveEvents();
            renderCalendar(); // Re-render to update event dots
            displayEventsForSelectedDate(); // Refresh event list
        }
    }

    // --- Event Handling ---

    prevMonthBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    });

    nextMonthBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    });

    eventForm.addEventListener('submit', (e) => {
        e.preventDefault();

        const title = eventTitleInput.value.trim();
        const date = eventDateInput.value;
        const time = eventTimeInput.value;
        const description = eventDescriptionInput.value.trim();

        if (!title || !date) {
            // Using Bootstrap modal for alert instead of window.alert
            const alertModal = new bootstrap.Modal(document.getElementById('alertModal'));
            document.getElementById('alertModalBody').textContent = 'Please enter event title and date.';
            alertModal.show();
            return;
        }

        const newEvent = {
            id: getUniqueId(),
            title: title,
            date: date,
            time: time,
            description: description
        };

        if (!events[date]) {
            events[date] = [];
        }
        events[date].push(newEvent);
        saveEvents(); // Save to local storage

        // Close modal (if using Bootstrap modal)
        const modalElement = document.getElementById('addEventModal');
        const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
        modal.hide();

        // Clear form
        eventForm.reset();

        // Re-render calendar to show new event dot if applicable
        renderCalendar();

        // If the event date is the currently selected date, update event list
        if (getFormattedDate(selectedDate) === date) {
            displayEventsForSelectedDate();
        }
    });

    // Initialize calendar and select today's date
    window.onload = function() {
        renderCalendar();
        // Select today's date on initial load
        const todayFormatted = getFormattedDate(new Date());
        const todayDiv = document.querySelector(`.calendar-day[data-date="${todayFormatted}"]`);
        if (todayDiv) {
            selectDay(todayDiv);
        } else {
            // Fallback if today is in prev/next month (shouldn't happen with current logic)
            selectedDate = new Date(); // Reset selected date to current
            displayEventsForSelectedDate();
        }

        // Set default date in modal to selected date
        eventDateInput.value = getFormattedDate(selectedDate);

        // Update modal date input when a new day is selected
        document.querySelectorAll('.calendar-day').forEach(dayDiv => {
            if (!dayDiv.classList.contains('prev-month') && !dayDiv.classList.contains('next-month')) {
                dayDiv.addEventListener('click', () => {
                    eventDateInput.value = dayDiv.dataset.date;
                });
            }
        });
    };

    // When the modal is shown, set the default date to the currently selected date
    document.getElementById('addEventModal').addEventListener('show.bs.modal', function(event) {
        eventTitleInput.value = ''; // Clear title on modal open
        eventTimeInput.value = ''; // Clear time on modal open
        eventDescriptionInput.value = ''; // Clear description on modal open
        eventDateInput.value = getFormattedDate(selectedDate);
    });
</script>