<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Hamro Patro - Calendar</title>
    <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Mukta', sans-serif;
            background-color: #f5f5f5;
        }

        /* Navigation Styles */
        .nav {
            width: 100%;
            min-height: 72px;
            background-color: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 40px;
            border-bottom: 1px solid #e0e0e0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 1001;
        }

        .logo-icon {
            width: 45px;
            height: 45px;
            background-color: #dc2626;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            color: white;
            font-weight: 700;
            font-size: 12px;
            text-align: center;
            line-height: 1.2;
            padding: 4px;
        }

        .logo-text {
            font-size: 20px;
            font-weight: 700;
            color: #1a1a1a;
            text-decoration: none;
            letter-spacing: 0.5px;
        }

        .nav-center {
            flex: 1;
            display: flex;
            justify-content: center;
        }

        .nav-links {
            list-style: none;
            display: flex;
            align-items: center;
            gap: 35px;
            margin: 0;
        }

        .nav-links li {
            display: inline-block;
        }

        .nav-links li a {
            color: #1a1a1a;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            transition: color 0.3s ease;
            white-space: nowrap;
        }

        .nav-links li a:hover {
            color: #dc2626;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 20px;
            z-index: 1001;
        }

        .language-btn {
            background-color: transparent;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 600;
            color: #1a1a1a;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .language-btn:hover {
            background-color: #f3f4f6;
            border-color: #9ca3af;
        }

        .user-icon {
            width: 40px;
            height: 40px;
            background-color: #e5e7eb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .user-icon:hover {
            background-color: #d1d5db;
        }

        .user-icon svg {
            width: 24px;
            height: 24px;
            fill: #6b7280;
        }

        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
            gap: 5px;
            z-index: 1001;
        }

        .hamburger span {
            width: 28px;
            height: 3px;
            background-color: #1a1a1a;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .hamburger.active span:nth-child(1) {
            transform: rotate(45deg) translate(8px, 8px);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active span:nth-child(3) {
            transform: rotate(-45deg) translate(8px, -8px);
        }

        /* Container Fluid */
        .container-fluid {
            width: 100%;
            padding: 0;
        }

        /* Date Banner Section */
        .date-banner {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            padding: 20px 50px;
            position: relative;
            overflow: hidden;
        }

        .date-banner::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.1) 0%, transparent 60%);
            pointer-events: none;
        }

        .date-content {
            position: relative;
            z-index: 1;
            max-width: 1400px;
            margin: 0 auto;
        }

        .nepali-date {
            font-size: 42px;
            font-weight: 600;
            margin-bottom: 8px;
            line-height: 1.2;
        }

        .english-day {
            font-size: 38px;
            font-weight: 500;
            margin-bottom: 12px;
            opacity: 0.95;
        }

        .nepali-tithi {
            font-size: 22px;
            font-weight: 500;
            margin-bottom: 6px;
            opacity: 0.9;
        }

        .paksya-info {
            font-size: 18px;
            font-weight: 400;
            margin-bottom: 8px;
            opacity: 0.85;
        }

        .day-time {
            font-size: 16px;
            font-weight: 400;
            margin-bottom: 4px;
            opacity: 0.8;
        }

        .english-date {
            font-size: 16px;
            font-weight: 400;
            opacity: 0.75;
        }

        /* Calendar Navigation Section */
        .calendar-nav {
            background-color: #f9fafb;
            padding: 20px 50px;
            border-bottom: 1px solid #e5e7eb;
        }

        .calendar-nav-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .calendar-title {
            font-size: 18px;
            font-weight: 600;
            color: #6b7280;
        }

        .search-box {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-icon {
            width: 20px;
            height: 20px;
            fill: #6b7280;
        }

        .search-input {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 8px 16px;
            font-size: 14px;
            outline: none;
            width: 250px;
            transition: border-color 0.3s ease;
        }

        .search-input:focus {
            border-color: #dc2626;
        }

        .search-input::placeholder {
            color: #9ca3af;
        }

        /* Responsive Styles */
        @media (max-width: 991px) {
            .hamburger {
                display: flex;
                order: 2;
            }

            .logo {
                order: 1;
            }

            .nav-right {
                order: 3;
            }

            .nav-center {
                position: fixed;
                top: 72px;
                left: -100%;
                width: 100%;
                height: calc(100vh - 72px);
                background-color: #ffffff;
                flex-direction: column;
                justify-content: flex-start;
                padding: 30px 20px;
                transition: left 0.3s ease;
                overflow-y: auto;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            .nav-center.active {
                left: 0;
            }

            .nav-links {
                flex-direction: column;
                gap: 0;
                width: 100%;
                align-items: flex-start;
            }

            .nav-links li {
                width: 100%;
                border-bottom: 1px solid #f3f4f6;
            }

            .nav-links li a {
                display: block;
                padding: 18px 16px;
                font-size: 16px;
                width: 100%;
            }

            .nav-links li a:hover {
                background-color: #f9fafb;
            }

            .date-banner {
                padding: 35px 30px;
            }

            .calendar-nav {
                padding: 18px 30px;
            }

            .nepali-date {
                font-size: 36px;
            }

            .english-day {
                font-size: 32px;
            }

            .nepali-tithi {
                font-size: 20px;
            }

            .paksya-info {
                font-size: 16px;
            }

            .search-input {
                width: 200px;
            }
        }

        @media (max-width: 768px) {
            .nav {
                padding: 12px 20px;
            }

            .date-banner {
                padding: 30px 25px;
            }

            .calendar-nav {
                padding: 16px 25px;
            }

            .calendar-nav-content {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .nepali-date {
                font-size: 32px;
            }

            .english-day {
                font-size: 28px;
            }

            .nepali-tithi {
                font-size: 18px;
            }

            .paksya-info {
                font-size: 15px;
            }

            .day-time {
                font-size: 14px;
            }

            .english-date {
                font-size: 14px;
            }

            .search-box {
                width: 100%;
            }

            .search-input {
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .date-banner {
                padding: 25px 20px;
            }

            .calendar-nav {
                padding: 14px 20px;
            }

            .nepali-date {
                font-size: 28px;
            }

            .english-day {
                font-size: 24px;
            }

            .nepali-tithi {
                font-size: 16px;
            }

            .paksya-info {
                font-size: 14px;
            }

            .day-time {
                font-size: 13px;
            }

            .english-date {
                font-size: 13px;
            }

            .calendar-title {
                font-size: 16px;
            }

            .language-btn {
                padding: 6px 12px;
                font-size: 13px;
            }

            .user-icon {
                width: 36px;
                height: 36px;
            }

            .user-icon svg {
                width: 20px;
                height: 20px;
            }
        }

        @media (max-width: 375px) {
            .logo-text {
                display: none;
            }

            .nav-right {
                gap: 8px;
            }

            .date-banner {
                padding: 20px 16px;
            }

            .calendar-nav {
                padding: 12px 16px;
            }

            .nepali-date {
                font-size: 24px;
            }

            .english-day {
                font-size: 22px;
            }

            .nepali-tithi {
                font-size: 15px;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="nav">
        <div class="logo">
            <div class="logo-icon">
                हाम्रो<br>पात्रो
            </div>
            <a href="/" class="logo-text">HAMRO PATRO</a>
        </div>

        <div class="hamburger" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <div class="nav-center" id="navCenter">
            <ul class="nav-links">
                <li><a href="#">Remit</a></li>
                <li><a href="#">Mart</a></li>
                <li><a href="#">Gifts</a></li>
                <li><a href="#">Recharge</a></li>
                <li><a href="#">Health</a></li>
                <li><a href="#">Jyotish</a></li>
                <li><a href="#">Rashifal</a></li>
                <li><a href="#">Podcasts</a></li>
                <li><a href="#">News</a></li>
                <li><a href="#">Blog</a></li>
                <li><a href="#">Gold/Silver</a></li>
                <li><a href="#">Forex</a></li>
                <li><a href="#">Converter</a></li>
            </ul>
        </div>

        <div class="nav-right">
            <button class="language-btn">EN</button>
            <div class="user-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path
                        d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                </svg>
            </div>
        </div>
    </nav>

    <!-- Main Content Container Fluid -->
    <div class="container-fluid">
        <!-- Date Banner Section -->
        <div class="date-banner">
            <div class="date-content">
                <div class="nepali-date" id="nepaliDate">३ पौष २०८२,</div>
                <div class="english-day" id="englishDay">Thursday</div>
                <div class="nepali-tithi" id="nepaliTithi">पुष कृष्ण चतुर्दंशी</div>
                <div class="paksya-info" id="paksyaInfo">पक्ष्य: Chaturdashi</div>
                <div class="day-time" id="dayTime">Day 03:13:14</div>
                <div class="english-date" id="englishDate">Dec 18, 2025</div>
            </div>
        </div>

        <!-- Calendar Navigation Section -->
        <div class="calendar-nav">
            <div class="calendar-nav-content">
                <div class="calendar-title">Nepali Calendar 2082</div>
                <div class="search-box">
                    <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path
                            d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
                    </svg>
                    <input type="text" class="search-input" placeholder="Search events">
                </div>
            </div>
        </div>

        <!-- Your other content can go here -->
    </div>

    <script>
        // Hamburger menu toggle
        const hamburger = document.getElementById('hamburger');
        const navCenter = document.getElementById('navCenter');

        hamburger.addEventListener('click', function() {
            hamburger.classList.toggle('active');
            navCenter.classList.toggle('active');
        });

        const navLinks = document.querySelectorAll('.nav-links li a');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                hamburger.classList.remove('active');
                navCenter.classList.remove('active');
            });
        });

        document.addEventListener('click', function(event) {
            const isClickInsideNav = navCenter.contains(event.target);
            const isClickOnHamburger = hamburger.contains(event.target);

            if (!isClickInsideNav && !isClickOnHamburger && navCenter.classList.contains('active')) {
                hamburger.classList.remove('active');
                navCenter.classList.remove('active');
            }
        });

        // Update date and time
        function updateDateTime() {
            const now = new Date();

            const options = {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            };
            document.getElementById('englishDate').textContent = now.toLocaleDateString('en-US', options);

            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            document.getElementById('englishDay').textContent = days[now.getDay()];

            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('dayTime').textContent = `Day ${hours}:${minutes}:${seconds}`;
        }

        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>
</body>

</html>
