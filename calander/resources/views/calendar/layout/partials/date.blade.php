<div class="date-banner" style="background-color: {{ setting('logo_color') }}">
    <div class="date-content">


        <div class="nepali-date" id="nepaliDate">

        </div>


        <div class="english-day" id="englishDay">

            Thursday
        </div>


        <div class="nepali-tithi" id="nepaliTithi">
            पुष कृष्ण चतुर्दंशी
        </div>


        <div class="paksya-info" id="paksyaInfo">
            पक्ष्य: Chaturdashi
        </div>


        <div class="day-time" id="dayTime">
            Day 03:13:14
        </div>


        <div class="english-date" id="englishDate">
            Dec 18, 2025
        </div>
    </div>

    <div class="ads-slider">
    @foreach(getSliders() as $index => $slider)
        <div class="ad-item">
            <img
                src="{{ asset('storage/' . $slider) }}"
                alt="slider{{ $loop->iteration }}"
                loading="lazy"
                decoding="async"
                class="img-fluid"
            >
        </div>
    @endforeach
</div>

</div>
@push('scripts')
<script>
    window.addEventListener('load', function () {
        var $slider = $('.ads-slider');
        if (!$slider.length) return;

        $slider.off('init.slickReady').on('init.slickReady', function () {
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
</script>
@endpush
