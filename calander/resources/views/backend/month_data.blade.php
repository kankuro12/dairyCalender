<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<style>
    .editable input {
        width: 100%;
        height: 100%;
        border: none;
        padding: 6px;
        font-size: inherit;
        font-family: inherit;
        background: transparent;
        outline: none;
    }

    .loading-overlay {
        position: fixed;
        inset: 0;
        background: rgba(255, 255, 255, 0.8);
        z-index: 9999;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .spinner {
        width: 48px;
        height: 48px;
        border: 4px solid #ddd;
        border-top-color: #198754;
        border-radius: 50%;
        animation: spin 0.9s linear infinite;
    }

    .loading-text {
        margin-top: 10px;
        font-size: 14px;
        color: #333;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>

<body>
    <button id="fetchapi">Fetch API Data</button>

    <div id="loadingOverlay" class="loading-overlay" style="display:none;">
        <div class="spinner"></div>
        <div class="loading-text">Fetching data…</div>
    </div>

    <h1>Add Events for Month: {{ $month }}</h1>

    <form method="POST" action="{{ route('add.month.data', ['month' => $month]) }}" id="monthForm">
        @csrf
        <input type="hidden" name="month" value="{{ $month }}">
        <table border="1" cellpadding="10" id="calendarTable">
            <tr>
                <th>Sn</th>
                <th>Bs Date</th>
                <th>AD Date</th>
                <th>Event</th>
                <th>Holiday</th>
                <th>Extra Event</th>
                <th>Date Text</th>
                <th>Notes</th>
            </tr>
            @for ($i = 1; $i <= $daysInMonth; $i++)
                @php
                    $datekey = "{$year}-{$month}-$i";
                    $day = $events[$datekey] ?? null;

                @endphp
                <tr data-date="{{ $datekey }}">
                    <td>{{ $i }}</td>
                    <td>{{ $datekey }}</td>
                    <td class="addate" data-field="ad-date" id='ad-date'>
                    </td>
                    {{-- @if ($day)
                        <td class="editable" data-field="event">{{ ($day['date'] ?? '') !== '--' ? $day['date'] : '' }}
                        </td>
                        <td class="editable" data-field="holiday">{{ $day['holiday'] ? 'Yes' : '' }}</td>
                        <td class="editable" data-field="extra_event">{{ $day['event'] ?? '' }}</td>
                        <td class="editable" data-field="date_text">{{ $day['text'] ?? '' }}</td>
                        <td class="editable" data-field="notes"></td>
                    {{-- @else --}}
                    <td class="editable" data-field="event">{{ $day && $day->title !== '--' ? $day->title : '' }}</td>
                    <td class="editable" data-field="holiday">
                        {{ $day && $day->is_holiday ? 'Yes' : '' }}
                    </td>
                    <td class="editable" data-field="extra_event">
                        {{ $day ? $day->extra_events : '' }}
                    </td>
                    <td class="editable" data-field="date_text">
                        {{ $day ? $day->tithi : '' }}
                    </td>
                    <td class="editable" data-field="notes">
                        {{ $day ? $day->notes : '' }}
                    </td>
                    <input type ="hidden" class="ad-date-input">
                    {{-- @endif --}}
                </tr>
            @endfor
        </table>
        <br>
        <button type="submit" class="btn btn-success">Save Month Data</button>
    </form>
    <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.js" type="text/javascript"></script>
    <script src={{ asset('js/nepali.datepicker.min.js') }}></script>
    <script>
        // const daysInMonth = NepaliFunctions.BS.GetDaysInMonth({{ $year }}, {{ $month }});
        // document.querySelectorAll('.addate').forEach((td, index) => {
        //     const adDate = NepaliFunctions.BS2AD(`{{ $year }}-{{ $month }}-${index + 1}`);
        //     td.innerText = adDate;
        //     // `${adDate.year}-${String(adDate.month).padStart(2, '0')}-${String(adDate.day).padStart(2, '0')}`;
        // });
        document.querySelectorAll('#calendarTable tr[data-date]').forEach((row, index) => {

            const bsDate = row.getAttribute('data-date');
            const adDate = NepaliFunctions.BS2AD(`{{ $year }}-{{ $month }}-${index+1}`);
            if (!adDate) return;

            const adTd = row.querySelector('.addate');
            if (adTd) adTd.innerText = adDate;
            row.querySelector('.ad-date-input').value = adDate;



        });
    </script>
    <script>
        document.addEventListener('click', function(e) {
            const td = e.target.closest('.editable');
            if (!td || td.querySelector('input')) return;
            const value = td.innerText.trim();
            const input = document.createElement('input');
            input.type = 'text';
            input.value = value;
            td.innerText = '';
            td.appendChild(input);
            input.focus();
            input.addEventListener('blur', () => {
                td.textContent = input.value;

            });
            input.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    input.blur();
                }
            });
        });
        document.getElementById('monthForm').addEventListener('submit', function() {
            document.querySelectorAll('#calendarTable tr[data-date]').forEach(row => {
                const bsDate = row.dataset.date;
                const adDate = row.querySelector('.ad-date-input').value;
                //data lai send garene structure ma
                const adInput = document.createElement('input');
                adInput.type = 'hidden';
                adInput.name = `data[${bsDate}][ad_date]`;
                adInput.value = adDate;
                this.appendChild(adInput);
                row.querySelectorAll('.editable').forEach(td => {
                    const field = td.getAttribute('data-field');
                    const input = td.querySelector('input');
                    const value = input ? input.value.trim() : td.innerText.trim();
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = `data[${bsDate}][${field}]`;
                    hiddenInput.value = value;
                    document.getElementById('monthForm').appendChild(hiddenInput);
                });
            });
        })
        const loadingOverlay = document.getElementById('loadingOverlay');

        function showLoader() {
            loadingOverlay.style.display = 'flex';
        }

        function hideLoader() {
            loadingOverlay.style.display = 'none';
        }

        function toggleEditing(disabled) {
            document.querySelectorAll('.editable').forEach(td => {
                td.style.pointerEvents = disabled ? 'none' : 'auto';
                td.style.opacity = disabled ? '0.6' : '1';
            });
        }
        const month = {{ $month }};

        const fetchApiBtn = document.getElementById('fetchapi');
        fetchApiBtn.addEventListener('click', function() {
            const message = prompt(
                'Are you sure you want to fetch API data? This will overwrite current table data. yes/no');
            if (message.toLowerCase() == 'yes') {
                showLoader();
                toggleEditing(true);
                $.ajax({
                    url: `/admin/api/show-month-data/${month}`,
                    type: 'GET',
                    success: function(response) {
                        populateTable(response);
                        console.log(response);
                    },
                    error: function(error) {
                        console.error('Error fetching API data:', error);
                        alert('Error fetching API data: ' + error.message);
                    },
                    complete: function() {
                        hideLoader();
                        toggleEditing(false);
                    }

                });
            } else {
                return;
            }
        });

        function populateTable(data) {
            document.querySelectorAll('#calendarTable tr[data-date]').forEach(row => {

                const date = row.dataset.date;
                const apiDay = data[date];
                if (!apiDay) {
                    row.querySelector('editable').forEach(td => {
                        td.innerText = '';
                    });
                }
                row.querySelector('[data-field="event"]').innerText = apiDay.date !== '--' ? apiDay.date : '';
                row.querySelector('[data-field="holiday"]').innerText = apiDay.holiday ? 'Yes' : '';
                row.querySelector('[data-field="extra_event"]').innerText = apiDay.event || '';
                row.querySelector('[data-field="date_text"]').innerText = apiDay.text || '';
                row.querySelector('[data-field="notes"]').innerText = '';
            });
        }
    </script>
</body>

</html>
