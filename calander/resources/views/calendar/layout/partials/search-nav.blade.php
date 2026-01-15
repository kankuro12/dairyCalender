<div class="calendar-nav">
    <div class="calendar-nav-content">
        <div class="calendar-title">{{ __('site.Nepali') }} {{ __('site.Calendar') }} 2082</div>

        <div class="teleprompter-wrapper" data-teleprompter data-speed="90" aria-label="Announcements">
            @if (!$announcements->isEmpty())
                <div class="teleprompter-track" data-track>
                    <div class="teleprompter-content" data-content>
                        @foreach ($announcements as $a)
                            <span class="announcement-item">
                                <span class="type {{ $a->type }}">{{ strtoupper($a->type) }}:</span>
                                <span class="announcement-title">{{ $a->title }}</span>
                            </span>
                            <span class="announcement-sep"
                                aria-hidden="true">&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>




<script>
    document.addEventListener('DOMContentLoaded', function() {
        const wrappers = document.querySelectorAll('[data-teleprompter]');
        wrappers.forEach(function(wrapper) {
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
                // Prefer precise layout width; fall back to scrollWidth.
                contentWidth = (content.getBoundingClientRect && content.getBoundingClientRect()
                    .width) || content.scrollWidth || 0;
                // Start from the right edge (first character enters immediately from right boundary)
                x = Math.round(wrapper.clientWidth || 0);
            }

            function ensureMeasured(triesLeft) {
                measure();
                if (contentWidth > 0) return true;
                if (triesLeft <= 0) return false;
                setTimeout(function() {
                    ensureMeasured(triesLeft - 1);
                }, 50);
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

                // Seamless wrap: ensure we wrap by exact content widths (handle large dt and floating-point drift).
                while (contentWidth > 0 && x <= -contentWidth) {
                    x += contentWidth;
                }

                // Round the transform to avoid sub-pixel rendering gaps/jitter.
                const tx = Math.round(x);
                track.style.transform = `translate3d(${tx}px, -50%, 0)`;
                requestAnimationFrame(frame);
            }

            requestAnimationFrame(frame);

            // Keep it correct on resize (mobile rotation etc.)
            if (window.ResizeObserver) {
                const ro = new ResizeObserver(function() {
                    measure();
                });
                ro.observe(wrapper);
            } else {
                window.addEventListener('resize', function() {
                    measure();
                });
            }
        });
    });
</script>
