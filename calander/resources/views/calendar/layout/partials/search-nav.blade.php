<div class="calendar-nav">
    <div class="calendar-nav-content">
        <div class="calendar-title">{{ __('site.Nepali') }} {{ __('site.Calendar') }} 2082</div>

        <div class="teleprompter-wrapper" data-teleprompter data-speed="90" aria-label="Announcements">
            @if(!$announcements->isEmpty())
                <div class="teleprompter-track" data-track>
                    <div class="teleprompter-content" data-content>
                        @foreach($announcements as $a)
                            <span class="announcement-item">
                                <span class="type {{ $a->type }}">{{ strtoupper($a->type) }}:</span>
                                <span class="announcement-title">{{ $a->title }}</span>
                            </span>
                            <span class="announcement-sep" aria-hidden="true">&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
<style>
    .teleprompter-wrapper {
        position: relative;
        overflow: hidden;
        flex: 1;
        height: 30px;
        min-width: 0;
    }

    .teleprompter-track {
        position: absolute;
        left: 0;
        top: 50%;
        transform: translate3d(0, -50%, 0);
        display: inline-flex;
        white-space: nowrap;
        will-change: transform;
    }

    .teleprompter-content {
        display: inline-flex;
        align-items: baseline;
    }

    /* Announcement styles */
    .announcement-item {
        display: inline-flex;
        align-items: baseline;
        gap: 6px;
        font-size: 14px;
        color: #111827;
    }

    .announcement-title {
        color: #374151;
    }

    .announcement-sep {
        color: #9ca3af;
        font-size: 14px;
    }

    .type {
        font-weight: 700;
        margin-right: 4px;
    }

    .type.news { color: #1d4ed8; }
    .type.announcement { color: #dc2626; }
    .type.alert { color: #f59e0b; }

    @media (prefers-reduced-motion: reduce) {
        /* Keep readable (user can still see it). We don't force-stop JS here. */
    }

    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const wrappers = document.querySelectorAll('[data-teleprompter]');
        wrappers.forEach(function (wrapper) {
            const track = wrapper.querySelector('[data-track]');
            const content = wrapper.querySelector('[data-content]');
            if (!track || !content) return;

            // Clone DOM nodes (no HTML string slicing/manipulation).
            const clone = content.cloneNode(true);
            clone.setAttribute('aria-hidden', 'true');
            track.appendChild(clone);

            let contentWidth = 0;
            let x = 0;
            let last = 0;

            // speed in px/sec (optional control via data-speed)
            const speed = Math.max(10, Number(wrapper.dataset.speed || 90));

            function measure() {
                contentWidth = content.scrollWidth || 0;
                // Start from the right edge (first character enters immediately from right boundary)
                x = wrapper.clientWidth || 0;
            }

            function ensureMeasured(triesLeft) {
                measure();
                if (contentWidth > 0) return true;
                if (triesLeft <= 0) return false;
                setTimeout(function () { ensureMeasured(triesLeft - 1); }, 50);
                return false;
            }

            // Give layout/fonts a moment if needed.
            ensureMeasured(10);

            function frame(now) {
                if (!last) last = now;
                const dt = Math.min(0.05, (now - last) / 1000);
                last = now;

                // If width is still not measurable, keep trying silently.
                if (!contentWidth) {
                    contentWidth = content.scrollWidth || 0;
                }

                x -= speed * dt;

                // Seamless wrap: shift by exactly one content width (no reset jump).
                if (contentWidth > 0 && x <= -contentWidth) {
                    x += contentWidth;
                }

                track.style.transform = `translate3d(${x}px, -50%, 0)`;
                requestAnimationFrame(frame);
            }

            requestAnimationFrame(frame);

            // Keep it correct on resize (mobile rotation etc.)
            if (window.ResizeObserver) {
                const ro = new ResizeObserver(function () {
                    measure();
                });
                ro.observe(wrapper);
            } else {
                window.addEventListener('resize', function () {
                    measure();
                });
            }
        });
    });
</script>

