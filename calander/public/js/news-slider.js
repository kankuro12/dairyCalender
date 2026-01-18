// News slider functionality
$(document).ready(function () {
    const $sliderTrack = $('#sliderTrack');
    const $nextBtn = $('#nextBtn');
    const $prevBtn = $('#prevBtn');
    const $sliderWrapper = $('.slider-wrapper');

    if (!$sliderTrack.length || !$nextBtn.length || !$prevBtn.length) {
        console.warn('News slider elements not found');
        return;
    }

    const $slides = $('.slide-item');
    const originalSlidesCount = $slides.length;

    // Calculate how many slides fit in viewport
    const containerWidth = $sliderWrapper.width();
    const slideWidth = 295; // 280px width + 15px gap
    const visibleSlides = Math.floor(containerWidth / slideWidth);

    // Only enable slider if we have more slides than can fit on screen
    const needsSliding = originalSlidesCount > visibleSlides;

    if (!needsSliding) {
        // Hide navigation buttons and disable sliding
        $nextBtn.hide();
        $prevBtn.hide();
        return;
    }

    let currentPosition = 0;
    const totalWidth = originalSlidesCount * slideWidth;

    // Clone slides for infinite loop effect only if we have enough items
    const $clonedSlides = $slides.clone();
    $sliderTrack.append($clonedSlides);

    function slideNext() {
        currentPosition += slideWidth;
        $sliderTrack.css('transform', `translateX(-${currentPosition}px)`);

        if (currentPosition >= totalWidth) {
            setTimeout(function () {
                $sliderTrack.css('transition', 'none');
                currentPosition = 0;
                $sliderTrack.css('transform', 'translateX(0)');

                setTimeout(function () {
                    $sliderTrack.css('transition', 'transform 0.5s ease');
                }, 50);
            }, 500);
        }
    }

    function slidePrev() {
        if (currentPosition <= 0) {
            $sliderTrack.css('transition', 'none');
            currentPosition = totalWidth;
            $sliderTrack.css('transform', `translateX(-${currentPosition}px)`);

            setTimeout(function () {
                $sliderTrack.css('transition', 'transform 0.5s ease');
                currentPosition -= slideWidth;
                $sliderTrack.css('transform', `translateX(-${currentPosition}px)`);
            }, 50);
        } else {
            currentPosition -= slideWidth;
            $sliderTrack.css('transform', `translateX(-${currentPosition}px)`);
        }
    }

    $nextBtn.on('click', slideNext);
    $prevBtn.on('click', slidePrev);

    $(document).on('keydown', function (e) {
        if (e.key === 'ArrowLeft') {
            slidePrev();
        } else if (e.key === 'ArrowRight') {
            slideNext();
        }
    });

    let touchStartX = 0;
    let touchEndX = 0;

    $sliderTrack.on('touchstart', function (e) {
        touchStartX = e.touches[0].clientX;
    });

    $sliderTrack.on('touchmove', function (e) {
        touchEndX = e.touches[0].clientX;
    });

    $sliderTrack.on('touchend', function () {
        if (touchStartX - touchEndX > 50) {
            slideNext();
        }
        if (touchEndX - touchStartX > 50) {
            slidePrev();
        }
    });
});
