/* ===================================
   ADMIN DASHBOARD - CORE FUNCTIONALITY
   =================================== */

$(document).ready(function () {

    // ===================================
    // SIDEBAR FUNCTIONALITY
    // ===================================
    function initSidebarToggle() {
        if ($(window).width() <= 991 && !$('.mobile-menu-btn').length) {
            const menuBtn = $('<button class="mobile-menu-btn"><i class="fas fa-bars"></i></button>');
            $('.top-header').prepend(menuBtn);

            menuBtn.on('click', function () {
                $('#sidebar').toggleClass('show');
                $('body').toggleClass('sidebar-open');
            });
        }

        $(document).on('click', function (e) {
            if ($(window).width() <= 991) {
                if (!$(e.target).closest('#sidebar, .mobile-menu-btn').length) {
                    $('#sidebar').removeClass('show');
                    $('body').removeClass('sidebar-open');
                }
            }
        });
    }

    $('.nav-link').on('click', function () {
        if ($(window).width() <= 991) {
            $('#sidebar').removeClass('show');
            $('body').removeClass('sidebar-open');
        }
    });

    // ===================================
    // RESPONSIVE HANDLING
    // ===================================
    let resizeTimer;
    $(window).on('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            if ($(window).width() > 991) {
                $('.mobile-menu-btn').remove();
                $('#sidebar').removeClass('show');
                $('body').removeClass('sidebar-open');
            } else {
                initSidebarToggle();
            }
        }, 250);
    });

    // Add styles
    const styles = `
        <style>
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
                .mobile-menu-btn { display: block; }
                body.sidebar-open { overflow: hidden; }
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
    $('head').append(styles);

    // ===================================
    // INITIALIZATION
    // ===================================
    initSidebarToggle();
    console.log('Admin Dashboard initialized');

});
