/* ===================================
   ADMIN DASHBOARD - INTERACTIVITY
   =================================== */

$(document).ready(function () {

    // ===================================
    // SIDEBAR FUNCTIONALITY
    // ===================================

    /**
     * Toggle sidebar on mobile
     */
    function initSidebarToggle() {
        // Create mobile menu button if it doesn't exist
        if ($(window).width() <= 991 && !$('.mobile-menu-btn').length) {
            const menuBtn = $('<button class="mobile-menu-btn"><i class="fas fa-bars"></i></button>');
            $('.top-header').prepend(menuBtn);

            menuBtn.on('click', function () {
                $('#sidebar').toggleClass('show');
                $('body').toggleClass('sidebar-open');
            });
        }

        // Close sidebar when clicking outside on mobile
        $(document).on('click', function (e) {
            if ($(window).width() <= 991) {
                if (!$(e.target).closest('#sidebar, .mobile-menu-btn').length) {
                    $('#sidebar').removeClass('show');
                    $('body').removeClass('sidebar-open');
                }
            }
        });
    }

    /**
     * Active menu item highlighting
     */
    $('.nav-link').on('click', function (e) {


        // Remove active class from all items


        // Add active class to clicked item


        // Close sidebar on mobile after clicking
        if ($(window).width() <= 991) {
            $('#sidebar').removeClass('show');
            $('body').removeClass('sidebar-open');
        }
    });

    // ===================================
    // TEXTAREA AUTO-RESIZE
    // ===================================

    /**
     * Auto-resize textarea as content grows
     */
    $('.form-textarea').on('input', function () {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // ===================================
    // CHARACTER COUNTER
    // ===================================

    /**
     * Update character count in real-time
     */
    $('.form-textarea').on('input', function () {
        const maxLength = 2200;
        const currentLength = $(this).val().length;
        const counterText = `${currentLength} / ${maxLength}`;

        $('.count-text').text(counterText);

        // Change color if approaching or exceeding limit
        if (currentLength > maxLength) {
            $('.count-text').css('color', 'var(--error-red)');
        } else if (currentLength > maxLength * 0.9) {
            $('.count-text').css('color', 'var(--text-secondary)');
        } else {
            $('.count-text').css('color', 'var(--text-muted)');
        }

        // Update preview caption
        updatePreviewCaption($(this).val());
    });

    /**
     * Update preview caption text
     */
    function updatePreviewCaption(text) {
        $('.preview-caption').text(text);
    }

    // ===================================
    // BUTTON INTERACTIONS
    // ===================================

    /**
     * Back button functionality
     */
    $('.btn-back').on('click', function (e) {
        e.preventDefault();
        // Add your back navigation logic here
        console.log('Back button clicked');
    });

    /**
     * Improve button with loading state
     */
    $('.btn-improve').on('click', function (e) {
        e.preventDefault();
        const $btn = $(this);
        const originalText = $btn.html();

        // Show loading state
        $btn.prop('disabled', true);
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Improving...');

        // Simulate API call (replace with actual API call)
        setTimeout(function () {
            $btn.prop('disabled', false);
            $btn.html(originalText);

            // Show success feedback
            showToast('Caption improved successfully!', 'success');
        }, 2000);
    });

    /**
     * Media upload buttons
     */
    $('.btn-upload').on('click', function (e) {
        e.preventDefault();
        // Trigger file input or open upload modal
        console.log('Upload clicked');
        showToast('Upload functionality would open here', 'info');
    });

    $('.btn-ai').on('click', function (e) {
        e.preventDefault();
        console.log('AI generation clicked');
        showToast('AI image generation would open here', 'info');
    });

    $('.btn-stock').on('click', function (e) {
        e.preventDefault();
        console.log('Stock images clicked');
        showToast('Stock image library would open here', 'info');
    });

    $('.btn-library').on('click', function (e) {
        e.preventDefault();
        console.log('Library clicked');
        showToast('Media library would open here', 'info');
    });

    /**
     * Schedule button
     */
    $('.btn-schedule').on('click', function (e) {
        e.preventDefault();
        const $btn = $(this);

        // Check if media is added (in real app)
        const hasMedia = false; // Replace with actual check

        if (!hasMedia) {
            showToast('Please add at least 1 media item', 'error');
            return;
        }

        console.log('Go to scheduling clicked');
        showToast('Redirecting to scheduling...', 'success');
    });

    /**
     * Chat button
     */
    $('.btn-chat').on('click', function (e) {
        e.preventDefault();
        console.log('Chat button clicked');
        showToast('Chat feature would open here', 'info');
    });

    // ===================================
    // PREVIEW CAROUSEL FUNCTIONALITY
    // ===================================

    /**
     * Image carousel navigation
     */
    let currentImageIndex = 0;
    const totalImages = 1; // Update based on actual images

    $('.carousel-prev').on('click', function () {
        if (currentImageIndex > 0) {
            currentImageIndex--;
            updateCarousel();
        }
    });

    $('.carousel-next').on('click', function () {
        if (currentImageIndex < totalImages - 1) {
            currentImageIndex++;
            updateCarousel();
        }
    });

    function updateCarousel() {
        // Update carousel display
        console.log(`Showing image ${currentImageIndex + 1} of ${totalImages}`);
    }

    // ===================================
    // INSTAGRAM ACTION BUTTONS
    // ===================================

    /**
     * Like, comment, share, bookmark interactions
     */
    $('.preview-actions .action-btn').on('click', function (e) {
        e.preventDefault();
        $(this).toggleClass('active');

        // Add visual feedback
        const icon = $(this).find('i');
        if ($(this).hasClass('active')) {
            icon.removeClass('far').addClass('fas');
            $(this).css('color', 'var(--primary-purple)');
        } else {
            icon.removeClass('fas').addClass('far');
            $(this).css('color', '');
        }
    });

    // ===================================
    // TOAST NOTIFICATION SYSTEM
    // ===================================

    /**
     * Show toast notification
     */
    function showToast(message, type = 'info') {
        // Remove existing toasts
        $('.toast-notification').remove();

        const toastColors = {
            success: '#4CAF50',
            error: '#E57373',
            warning: '#FFA726',
            info: '#8B7AB8'
        };

        const toast = $(`
            <div class="toast-notification" style="
                position: fixed;
                top: 24px;
                right: 24px;
                background: white;
                color: ${toastColors[type]};
                padding: 16px 24px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 9999;
                animation: slideInRight 0.3s ease;
                border-left: 4px solid ${toastColors[type]};
                font-size: 14px;
                font-weight: 500;
                max-width: 300px;
            ">
                ${message}
            </div>
        `);

        $('body').append(toast);

        // Auto remove after 3 seconds
        setTimeout(function () {
            toast.css('animation', 'slideOutRight 0.3s ease');
            setTimeout(function () {
                toast.remove();
            }, 300);
        }, 3000);
    }

    // Add CSS for toast animations
    const toastStyles = `
        <style>
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
            
            .mobile-menu-btn {
                background: transparent;
                border: none;
                color: var(--text-muted);
                font-size: 20px;
                padding: 8px;
                cursor: pointer;
                margin-right: 16px;
                display: none;
            }
            
            @media (max-width: 991px) {
                .mobile-menu-btn {
                    display: block;
                }
                
                body.sidebar-open {
                    overflow: hidden;
                }
                
                body.sidebar-open::before {
                    content: '';
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0, 0, 0, 0.5);
                    z-index: 999;
                }
            }
        </style>
    `;

    $('head').append(toastStyles);

    // ===================================
    // RESPONSIVE HANDLING
    // ===================================

    /**
     * Handle window resize
     */
    let resizeTimer;
    $(window).on('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            // Remove mobile menu button on desktop
            if ($(window).width() > 991) {
                $('.mobile-menu-btn').remove();
                $('#sidebar').removeClass('show');
                $('body').removeClass('sidebar-open');
            } else {
                initSidebarToggle();
            }
        }, 250);
    });

    // ===================================
    // SMOOTH SCROLLING
    // ===================================

    /**
     * Smooth scroll for anchor links
     */
    $('a[href^="#"]').on('click', function (e) {
        const target = $(this.getAttribute('href'));
        if (target.length) {
            e.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 80
            }, 600);
        }
    });

    // ===================================
    // KEYBOARD SHORTCUTS
    // ===================================

    /**
     * Add keyboard shortcuts for common actions
     */
    $(document).on('keydown', function (e) {
        // Cmd/Ctrl + S to save (schedule)
        if ((e.metaKey || e.ctrlKey) && e.key === 's') {
            e.preventDefault();
            $('.btn-schedule').trigger('click');
        }

        // Cmd/Ctrl + I to improve caption
        if ((e.metaKey || e.ctrlKey) && e.key === 'i') {
            e.preventDefault();
            $('.btn-improve').trigger('click');
        }

        // Esc to close sidebar on mobile
        if (e.key === 'Escape') {
            $('#sidebar').removeClass('show');
            $('body').removeClass('sidebar-open');
        }
    });

    // ===================================
    // FORM VALIDATION
    // ===================================

    /**
     * Validate form before submission
     */
    function validateForm() {
        let isValid = true;
        const caption = $('.form-textarea').val().trim();

        if (caption.length === 0) {
            showToast('Please enter a caption', 'error');
            isValid = false;
        }

        if (caption.length > 2200) {
            showToast('Caption exceeds maximum length', 'error');
            isValid = false;
        }

        // Check for media (would need actual implementation)
        // if (!hasMedia()) {
        //     showToast('Please add at least 1 media item', 'error');
        //     isValid = false;
        // }

        return isValid;
    }

    // ===================================
    // AUTOSAVE FUNCTIONALITY
    // ===================================

    /**
     * Auto-save draft every 30 seconds
     */
    let autosaveTimer;
    function startAutosave() {
        autosaveTimer = setInterval(function () {
            const caption = $('.form-textarea').val();

            if (caption.trim().length > 0) {
                // Save to localStorage or API
                localStorage.setItem('draft_caption', caption);
                console.log('Draft auto-saved');

                // Optional: Show subtle save indicator
                // showToast('Draft saved', 'success');
            }
        }, 30000); // 30 seconds
    }

    /**
     * Load saved draft on page load
     */
    function loadDraft() {
        const savedDraft = localStorage.getItem('draft_caption');
        if (savedDraft) {
            $('.form-textarea').val(savedDraft);
            $('.form-textarea').trigger('input'); // Update character count
        }
    }

    // ===================================
    // INITIALIZATION
    // ===================================

    // Initialize sidebar toggle for mobile
    initSidebarToggle();

    // Load saved draft
    loadDraft();

    // Start autosave
    startAutosave();

    // Set initial character count
    $('.form-textarea').trigger('input');

    console.log('Admin Dashboard initialized successfully');

    // ===================================
    // UTILITY FUNCTIONS
    // ===================================

    /**
     * Debounce function for performance
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    /**
     * Format date for display
     */
    function formatDate(date) {
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        return new Date(date).toLocaleDateString('en-US', options);
    }

    /**
     * Truncate text with ellipsis
     */
    function truncateText(text, maxLength) {
        if (text.length <= maxLength) return text;
        return text.substring(0, maxLength) + '...';
    }

});

// ===================================
// VANILLA JS FALLBACK (if jQuery fails)
// ===================================

if (typeof jQuery === 'undefined') {
    console.warn('jQuery not loaded. Using vanilla JavaScript fallback.');

    document.addEventListener('DOMContentLoaded', function () {
        // Add basic functionality without jQuery
        const textarea = document.querySelector('.form-textarea');
        if (textarea) {
            textarea.addEventListener('input', function () {
                const maxLength = 2200;
                const currentLength = this.value.length;
                const counterText = document.querySelector('.count-text');
                if (counterText) {
                    counterText.textContent = `${currentLength} / ${maxLength}`;
                }
            });
        }
    });
}