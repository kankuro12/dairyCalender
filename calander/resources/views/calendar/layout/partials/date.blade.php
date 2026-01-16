<div class="date-banner" style="background-color: {{ setting('logo_color') }}">
    <div class="date-content">

        <div class="nepali-date" id="nepaliDate">
            <!-- Today's Nepali date will be loaded here -->
        </div>

        <div class="english-day" id="englishDay">
            <!-- Today's English day will be loaded here -->
        </div>

        <div class="today-event" id="todayEvent">
            <strong></strong> <span id="eventTitle">-</span>
        </div>

        <div class="nepali-tithi" id="nepaliTithi">
            <strong></strong> <span id="tithiValue">-</span>
        </div>

        <div class="day-time" id="dayTime">
            <!-- Current time will be displayed here -->
        </div>

        <div class="english-date" id="englishDate">
            <strong>{{ __('site.english_date') }}:</strong> <span id="engDateValue">-</span>
        </div>
    </div>

    <div class="ads-slider">
        @foreach (getSliders() as $index => $slider)
            <div class="ad-item">
                <img src="{{ asset('storage/' . $slider) }}" alt="slider{{ $loop->iteration }}" loading="lazy"
                    decoding="async" class="img-fluid">
            </div>
        @endforeach
    </div>

</div>
@push('scripts')
    <script>
        window.addEventListener('load', function() {
            // Load today's date information
            loadTodayInfo();

            // Update time every second
            updateTime();
            setInterval(updateTime, 1000);

            // Slider initialization
            var $slider = $('.ads-slider');
            if (!$slider.length) return;

            $slider.off('init.slickReady').on('init.slickReady', function() {
                $slider.addClass('is-ready');
            });

            if (!$slider.hasClass('slick-initialized')) {
                $slider.slick({
                    infinite: true,
                    arrows: false,
                    autoplay: true,
                    autoplaySpeed: 2000,
                    fade: true,
                    cssEase: 'linear',
                });
            } else {
                $slider.addClass('is-ready');
            }
        });

        async function loadTodayInfo() {
            try {
                const today = NepaliFunctions.BS.GetCurrentDate();
                const bsYear = today.year;
                const bsMonth = today.month;
                const bsDay = today.day;

                // Get current language
                const currentLang = '{{ app()->getLocale() }}';

                // Format BS date
                const bsDateStr = `${bsYear}-${String(bsMonth).padStart(2, '0')}-${String(bsDay).padStart(2, '0')}`;

                // Display Nepali date
                const nepMonth = NepaliFunctions.BS.GetMonthInUnicode(bsMonth - 1);
                const nepYear = NepaliFunctions.ConvertToUnicode(bsYear);
                const nepDay = NepaliFunctions.ConvertToUnicode(bsDay);
                document.getElementById('nepaliDate').textContent = `${nepDay} ${nepMonth} ${nepYear}`;

                // Get and display English day
                const adDateStr = NepaliFunctions.BS2AD(bsDateStr);
                const adDate = new Date(adDateStr);

                // Weekday names in Nepali
                const nepaliDays = [
                    'आइतबार', // Sunday
                    'सोमबार', // Monday
                    'मंगलबार', // Tuesday
                    'बुधबार', // Wednesday
                    'बिहिबार', // Thursday
                    'शुक्रबार', // Friday
                    'शनिबार' // Saturday
                ];

                const dayOfWeek = adDate.getDay();

                if (currentLang === 'np') {
                    // Display in Nepali
                    document.getElementById('englishDay').textContent = nepaliDays[dayOfWeek];
                } else {
                    // Display in English
                    const englishDay = adDate.toLocaleDateString('en-US', {
                        weekday: 'long'
                    });
                    document.getElementById('englishDay').textContent = englishDay;
                }

                // Display English date
                const englishDate = adDate.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
                document.getElementById('engDateValue').textContent = englishDate;

                // Fetch today's event data
                const response = await fetch(`/calendar/data/${bsYear}/${bsMonth}`);
                const events = await response.json();

                if (events && events[bsDateStr]) {
                    const todayEvent = events[bsDateStr];

                    // Display event title
                    if (todayEvent.title) {
                        document.getElementById('eventTitle').textContent = todayEvent.title;
                    } else {
                        document.getElementById('eventTitle').textContent = '-';
                    }

                    // Display tithi
                    if (todayEvent.tithi) {
                        document.getElementById('tithiValue').textContent = todayEvent.tithi;
                    } else {
                        document.getElementById('tithiValue').textContent = '-';
                    }
                } else {
                    document.getElementById('eventTitle').textContent = '-';
                    document.getElementById('tithiValue').textContent = '-';
                }
            } catch (error) {
                console.error('Error loading today info:', error);
            }
        }

        function updateTime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const timeString = `${hours}:${minutes}:${seconds}`;
            document.getElementById('currentTime').textContent = timeString;
        }
    </script>
@endpush
