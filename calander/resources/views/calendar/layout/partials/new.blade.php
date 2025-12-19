<!DOCTYPE html>
<html lang="ne">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>समाचार बुलेटिन - News Slider</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .slider-container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .slider-header {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }

        .slider-wrapper {
            position: relative;
            overflow: hidden;
        }

        .slider-track {
            display: flex;
            gap: 15px;
            transition: transform 0.5s ease;
        }

        .slide-item {
            min-width: 280px;
            height: 240px;
            border-radius: 12px;
            position: relative;
            overflow: hidden;
            cursor: pointer;
            flex-shrink: 0;
            transition: transform 0.3s ease;
        }

        .slide-item:hover {
            transform: translateY(-5px);
        }

        .slide-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .slide-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0.4) 60%, transparent 100%);
            padding: 20px 15px 15px;
            color: white;
        }

        .slide-title {
            font-size: 16px;
            font-weight: 600;
            line-height: 1.4;
            margin-bottom: 10px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .slide-sources {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .source-icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            color: #333;
            border: 2px solid white;
        }

        .source-count {
            background: rgba(255, 255, 255, 0.3);
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 13px;
            font-weight: 600;
        }

        .nav-button {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            z-index: 10;
            transition: all 0.3s ease;
        }

        .nav-button:hover {
            background: #f0f0f0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .nav-button i {
            font-size: 18px;
            color: #333;
        }

        .nav-prev {
            left: -20px;
        }

        .nav-next {
            right: -20px;
        }

        @media (max-width: 768px) {
            .slide-item {
                min-width: 250px;
                height: 220px;
            }

            .nav-button {
                width: 35px;
                height: 35px;
            }

            .nav-prev {
                left: -10px;
            }

            .nav-next {
                right: -10px;
            }
        }
    </style>
</head>

<body>
    <div class="slider-container">
        <h2 class="slider-header">समाचार बुलेटिन</h2>

        <div class="slider-wrapper">
            <button class="nav-button nav-prev" id="prevBtn">
                <i class="fas fa-chevron-left"></i>
            </button>

            <div class="slider-track" id="sliderTrack">
                <!-- Slide 1 -->
                <div class="slide-item">
                    <img src="https://images.unsplash.com/photo-1464746133101-a2c3f88e0dd9?w=500&h=400&fit=crop"
                        alt="News">
                    <div class="slide-overlay">
                        <div class="slide-title">नेपालमा तुल्लोदेखिमा सिद्धार्थ देवी भारतको</div>
                        <div class="slide-sources">
                            <div class="source-icon" style="background: #1DA1F2;">🐦</div>
                            <div class="source-icon" style="background: #FF0000;">📺</div>
                            <div class="source-icon" style="background: #0077B5;">💼</div>
                            <div class="source-icon" style="background: #25D366;">📱</div>
                            <div class="source-count">+3</div>
                        </div>
                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="slide-item">
                    <img src="https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=500&h=400&fit=crop"
                        alt="News">
                    <div class="slide-overlay">
                        <div class="slide-title">गाहा सृङ्खलापछि हत्तुरुआमाको हत्या, रे पुज</div>
                        <div class="slide-sources">
                            <div class="source-icon" style="background: #FF4500;">📰</div>
                            <div class="source-icon" style="background: #00C4CC;">🌐</div>
                        </div>
                    </div>
                </div>

                <!-- Slide 3 -->
                <div class="slide-item">
                    <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=500&h=400&fit=crop"
                        alt="News">
                    <div class="slide-overlay">
                        <div class="slide-title">अर्थपालको हिंसाले भिकाडमा बाल्किए, आओसले बात्तिन</div>
                        <div class="slide-sources">
                            <div class="source-icon" style="background: #E60023;">📌</div>
                            <div class="source-icon" style="background: #FF0000;">▶️</div>
                            <div class="source-icon" style="background: #1DA1F2;">🐦</div>
                            <div class="source-icon" style="background: #333;">📱</div>
                            <div class="source-count">+1</div>
                        </div>
                    </div>
                </div>

                <!-- Slide 4 -->
                <div class="slide-item">
                    <img src="https://images.unsplash.com/photo-1523961131990-5ea7c61b2107?w=500&h=400&fit=crop"
                        alt="News">
                    <div class="slide-overlay">
                        <div class="slide-title">अमेरिकाको लागि रिमे कार्यक्रम चोलेन निर्णयमा</div>
                        <div class="slide-sources">
                            <div class="source-icon" style="background: #00C4CC;">🌐</div>
                        </div>
                    </div>
                </div>

                <!-- Slide 5 -->
                <div class="slide-item">
                    <img src="https://images.unsplash.com/photo-1520769669658-f07657f5a307?w=500&h=400&fit=crop"
                        alt="News">
                    <div class="slide-overlay">
                        <div class="slide-title">ट्रम्प महिला २०० जनासम्मको नागरिकता रद्द</div>
                        <div class="slide-sources">
                            <div class="source-icon" style="background: #E60023;">📌</div>
                            <div class="source-icon" style="background: #0077B5;">💼</div>
                            <div class="source-icon" style="background: #FF0000;">▶️</div>
                            <div class="source-icon" style="background: #FF4500;">📰</div>
                            <div class="source-count">+4</div>
                        </div>
                    </div>
                </div>

                <!-- Slide 6 -->
                <div class="slide-item">
                    <img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=500&h=400&fit=crop"
                        alt="News">
                    <div class="slide-overlay">
                        <div class="slide-title">अत्तिलिक अर्लाममा रोहे बालिष्टसले सुनाई चुनाई</div>
                        <div class="slide-sources">
                            <div class="source-icon" style="background: #E60023;">📌</div>
                            <div class="source-icon" style="background: #0077B5;">💼</div>
                            <div class="source-icon" style="background: #FF0000;">▶️</div>
                            <div class="source-icon" style="background: #333;">📱</div>
                        </div>
                    </div>
                </div>

                <!-- Slide 7 -->
                <div class="slide-item">
                    <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=500&h=400&fit=crop"
                        alt="News">
                    <div class="slide-overlay">
                        <div class="slide-title">काठमाडौं महानगरको नगरसभा पुरै सु मा</div>
                        <div class="slide-sources">
                            <div class="source-icon" style="background: #E60023;">📌</div>
                            <div class="source-icon" style="background: #00C4CC;">🌐</div>
                        </div>
                    </div>
                </div>

                <!-- Slide 8 -->
                <div class="slide-item">
                    <img src="https://images.unsplash.com/photo-1485738422979-f5c462d49f74?w=500&h=400&fit=crop"
                        alt="News">
                    <div class="slide-overlay">
                        <div class="slide-title">अमेरिकाको गम्को दिगो कार्यक्रम बन्द गर्ने घोषणा</div>
                        <div class="slide-sources">
                            <div class="source-icon" style="background: #1DA1F2;">🐦</div>
                            <div class="source-icon" style="background: #0077B5;">💼</div>
                            <div class="source-icon" style="background: #FF0000;">📺</div>
                            <div class="source-icon" style="background: #00C4CC;">🌐</div>
                            <div class="source-count">+1</div>
                        </div>
                    </div>
                </div>

                <!-- Slide 9 -->
                <div class="slide-item">
                    <img src="https://images.unsplash.com/photo-1477959858617-67f85cf4f1df?w=500&h=400&fit=crop"
                        alt="News">
                    <div class="slide-overlay">
                        <div class="slide-title">अमेरिकाले एकास्मे गनु</div>
                        <div class="slide-sources">
                            <div class="source-icon" style="background: #FF4500;">📰</div>
                            <div class="source-icon" style="background: #00C4CC;">🌐</div>
                        </div>
                    </div>
                </div>
            </div>

            <button class="nav-button nav-next" id="nextBtn">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script></script>
</body>

</html>
