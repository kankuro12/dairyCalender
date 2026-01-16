@extends('backend.layout.app')
@section('content')
    <div class="content-wrapper">
        <div class="dashboard-container">
            <!-- Dashboard Header -->
            <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1 class="page-title">Calendar Management</h1>
                    <p class="page-subtitle">Add and manage calendar events for all months</p>
                </div>
                <div class="today-date-display" style="text-align: right;">
                    <div style="font-size: 14px; color: var(--text-muted); margin-bottom: 4px;">
                        <i class="fas fa-calendar-day"></i> Today
                    </div>
                    <div id="todayNepaliDate" style="font-size: 18px; font-weight: 600; color: var(--text-primary);">
                        Loading...
                    </div>
                    <div id="todayYear" style="font-size: 13px; color: var(--text-muted); margin-top: 2px;">
                        BS Year: <span id="currentBsYear">{{ $currentBsYear ?? 2082 }}</span>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Quick Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon bg-purple">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-value">{{ $totalEvents }}</h3>
                        <p class="stat-label">Total Events</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon bg-blue">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-value">{{ $totalHolidays }}</h3>
                        <p class="stat-label">Holidays</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon bg-green">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-value">{{ $activeAnnouncements }}</h3>
                        <p class="stat-label">Active Announcements</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon bg-orange">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-value">12</h3>
                        <p class="stat-label">Months</p>
                    </div>
                </div>
            </div>

            <!-- Year and Month Selection Section -->
            <div class="content-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-calendar-plus"></i>
                        Manage Events by Year and Month
                    </h2>
                </div>

                <div class="card-body">
                    <form id="monthYearForm" class="selection-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="yearSelect" class="form-label">
                                    <i class="fas fa-calendar-day"></i> Select Year
                                </label>
                                <select id="yearSelect" class="form-select" required>
                                    <option value="">Select a year first...</option>
                                    @for ($year = 2075; $year <= 2085; $year++)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="monthSelect" class="form-label">
                                    <i class="fas fa-calendar"></i> Select Month
                                </label>
                                <select id="monthSelect" class="form-select" disabled required>
                                    <option value="">Select a year first...</option>
                                </select>
                            </div>

                            <div class="form-group form-action">
                                <button class="btn btn-primary" type="submit" id="manageEventBtn">
                                    <i class="fas fa-edit"></i>
                                    Manage Events
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="info-text" style="margin-top: 16px; color: var(--text-muted); font-size: 13px;">
                        <i class="fas fa-info-circle"></i> Select a year to see available months, then choose a month to
                        manage
                        its events.
                    </div>
                </div>
            </div>

            <!-- All Months Overview -->
            <div class="content-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-list"></i>
                        All Months Overview
                    </h2>
                    <div class="year-filter">
                        <label for="overviewYearSelect"
                            style="margin-right: 8px; font-size: 13px; color: var(--text-muted);">
                            <i class="fas fa-filter"></i> Filter by Year:
                        </label>
                        <select id="overviewYearSelect" class="form-select" style="width: auto; display: inline-block;">
                            <option value="">Select a year...</option>
                            @for ($year = 2075; $year <= 2085; $year++)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="card-body">
                    <div id="monthsOverviewContainer">
                        <div class="months-grid" id="monthsOverviewGrid">
                            <div class="empty-state-months">
                                <i class="fas fa-calendar-alt"></i>
                                <p>Please select a year to view months overview</p>
                            </div>
                        </div>
                    </div>

                    <div id="monthsLoading" style="display: none; text-align: center; padding: 48px;">
                        <i class="fas fa-spinner fa-spin" style="font-size: 32px; color: var(--primary-purple);"></i>
                        <p style="margin-top: 16px; color: var(--text-muted);">Loading months data...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/nepali.datepicker.min.js') }}"></script>
    <script>
        // Get current Nepali date and update the display
        (function updateCurrentNepaliDate() {
            try {
                const todaysDate = NepaliFunctions.BS.GetCurrentDate();
                const currentYear = todaysDate.year;
                const currentMonth = todaysDate.month;
                const currentDay = todaysDate.day;

                // Nepali month names
                const nepaliMonthNames = [
                    '', // 0 index placeholder
                    'बैशाख', 'जेष्ठ', 'आषाढ', 'श्रावण', 'भाद्र', 'आश्विन',
                    'कार्तिक', 'मंसिर', 'पौष', 'माघ', 'फाल्गुन', 'चैत्र'
                ];

                // Convert day to Nepali Unicode
                const nepaliDay = NepaliFunctions.ConvertToUnicode(currentDay);
                const monthName = nepaliMonthNames[currentMonth] || currentMonth;

                // Update the display
                const dateDisplay = document.getElementById('todayNepaliDate');
                if (dateDisplay) {
                    dateDisplay.textContent = `${monthName} ${nepaliDay}`;
                }

                // Update current year display
                const yearDisplay = document.getElementById('currentBsYear');
                if (yearDisplay) {
                    yearDisplay.textContent = currentYear;
                }

                console.log('[Dashboard] Current Nepali Date:', {
                    year: currentYear,
                    month: currentMonth,
                    day: currentDay,
                    formatted: `${monthName} ${nepaliDay}, ${currentYear}`
                });

                // Update stats if the year differs from server-side year
                const serverYear = {{ $currentBsYear ?? 2082 }};
                if (currentYear !== serverYear) {
                    console.log('[Dashboard] Year mismatch - fetching updated stats for year:', currentYear);
                    fetchStatsForYear(currentYear);
                }
            } catch (e) {
                console.error('[Dashboard] Error getting Nepali date:', e);
                const dateDisplay = document.getElementById('todayNepaliDate');
                if (dateDisplay) {
                    dateDisplay.textContent = 'Date unavailable';
                }
            }
        })();

        // Function to fetch and update stats for current year
        function fetchStatsForYear(year) {
            console.log('[Dashboard] Fetching stats for year:', year);

            fetch(`/admin/stats/${year}`)
                .then(response => response.json())
                .then(data => {
                    console.log('[Dashboard] Stats received:', data);

                    // Update Total Events
                    const totalEventsEl = document.querySelector('.stat-card:nth-child(1) .stat-value');
                    if (totalEventsEl) {
                        totalEventsEl.textContent = data.totalEvents;
                    }

                    // Update Total Holidays
                    const totalHolidaysEl = document.querySelector('.stat-card:nth-child(2) .stat-value');
                    if (totalHolidaysEl) {
                        totalHolidaysEl.textContent = data.totalHolidays;
                    }

                    console.log('[Dashboard] Stats updated successfully');
                })
                .catch(error => {
                    console.error('[Dashboard] Error fetching stats:', error);
                });
        }

        const yearSelect = document.getElementById('yearSelect');
        const monthSelect = document.getElementById('monthSelect');
        const monthYearForm = document.getElementById('monthYearForm');
        const overviewYearSelect = document.getElementById('overviewYearSelect');
        const monthsOverviewGrid = document.getElementById('monthsOverviewGrid');
        const monthsLoading = document.getElementById('monthsLoading');

        // Nepali month names
        const nepaliMonths = [{
                value: 1,
                name: 'बैशाख'
            },
            {
                value: 2,
                name: 'जेष्ठ'
            },
            {
                value: 3,
                name: 'आषाढ'
            },
            {
                value: 4,
                name: 'श्रावण'
            },
            {
                value: 5,
                name: 'भाद्र'
            },
            {
                value: 6,
                name: 'आश्विन'
            },
            {
                value: 7,
                name: 'कार्तिक'
            },
            {
                value: 8,
                name: 'मंसिर'
            },
            {
                value: 9,
                name: 'पौष'
            },
            {
                value: 10,
                name: 'माघ'
            },
            {
                value: 11,
                name: 'फाल्गुन'
            },
            {
                value: 12,
                name: 'चैत्र'
            }
        ];

        // When year is selected, enable and populate month selector
        yearSelect.addEventListener('change', function() {
            const year = this.value;

            if (!year) {
                monthSelect.disabled = true;
                monthSelect.innerHTML = '<option value="">Select a year first...</option>';
                return;
            }

            // Enable month selector and populate it
            monthSelect.disabled = false;
            monthSelect.innerHTML = '<option value="">Choose a month...</option>';

            nepaliMonths.forEach(month => {
                const option = document.createElement('option');
                option.value = month.value;
                option.textContent = month.name;
                monthSelect.appendChild(option);
            });
        });

        // Form submission - navigate to month data page
        monthYearForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const year = yearSelect.value;
            const month = monthSelect.value;

            if (!year || !month) {
                alert('Please select both year and month');
                return;
            }

            const daysInMonth = NepaliFunctions.BS.GetDaysInMonth(year, month);
            const url = `/admin/add-month-data/${month}?days=${daysInMonth}&year=${year}`;
            window.location.href = url;
        });

        // Overview year selector - load months for selected year
        overviewYearSelect.addEventListener('change', function() {
            const year = this.value;

            if (!year) {
                monthsOverviewGrid.innerHTML = `
                        <div class="empty-state-months">
                            <i class="fas fa-calendar-alt"></i>
                            <p>Please select a year to view months overview</p>
                        </div>
                    `;
                return;
            }

            // Show loading
            monthsOverviewGrid.style.display = 'none';
            monthsLoading.style.display = 'block';

            // Fetch month data for selected year
            fetch(`/admin/months-data/${year}`)
                .then(response => response.json())
                .then(data => {
                    monthsLoading.style.display = 'none';
                    monthsOverviewGrid.style.display = 'grid';

                    // Clear and populate grid
                    monthsOverviewGrid.innerHTML = '';

                    nepaliMonths.forEach(month => {
                        const eventCount = data.eventCounts[month.value] || 0;

                        const monthCard = document.createElement('div');
                        monthCard.className = 'month-card';
                        monthCard.innerHTML = `
                                <div class="month-icon">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div class="month-info">
                                    <h3 class="month-name">${month.name}</h3>
                                    <p class="month-stats">${eventCount} events</p>
                                </div>
                                <a href="#" class="btn btn-sm btn-outline quick-manage" data-month="${month.value}" data-year="${year}">
                                    <i class="fas fa-edit"></i>
                                    Manage
                                </a>
                            `;

                        monthsOverviewGrid.appendChild(monthCard);
                    });

                    // Attach event listeners to manage buttons
                    attachQuickManageListeners();
                })
                .catch(error => {
                    monthsLoading.style.display = 'none';
                    monthsOverviewGrid.style.display = 'block';
                    monthsOverviewGrid.innerHTML = `
                            <div class="empty-state-months">
                                <i class="fas fa-exclamation-triangle"></i>
                                <p>Error loading months data. Please try again.</p>
                            </div>
                        `;
                    console.error('Error:', error);
                });
        });

        // Quick manage buttons
        function attachQuickManageListeners() {
            document.querySelectorAll('.quick-manage').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const month = this.dataset.month;
                    const year = this.dataset.year || overviewYearSelect.value || 2082;

                    const daysInMonth = NepaliFunctions.BS.GetDaysInMonth(year, month);
                    const url = `/admin/add-month-data/${month}?days=${daysInMonth}&year=${year}`;
                    window.location.href = url;
                });
            });
        }
    </script>
@endpush
