<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dairy Patro </title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin/style.css') }}">
</head>

<body>

    @include('backend.layout.partials.sidebar')

    <!-- Main Content Area -->
    <div class="main-content" id="mainContent">
        <!-- Top Header -->
        <div class="top-header">
            {{-- <button class="btn-back">
                <i class="fas fa-arrow-left"></i>
            </button>

            <div class="header-actions">
                <button class="btn-icon" id="refresh">
                    <i class="fas fa-redo"></i>
                </button>

            </div> --}}
        </div>

        @yield('content')
        <!-- Content Section -->
        {{-- <div class="content-wrapper">
            <div class="row g-4">
                <!-- Left Column - Content Creation -->
                <div class="col-lg-6">
                    <div class="content-card">
                        <div class="instagram-header">
                            <i class="fab fa-instagram instagram-icon"></i>
                            <span class="platform-text">Instagram post</span>
                        </div>

                        <div class="form-section">
                            <label class="form-label">Caption</label>
                            <textarea class="form-textarea" rows="8" placeholder="Enter caption...">Drømmer du om at opgradere din sommergarderoben uden at sprænge budgettet? Så har vi godt nyt til dig! Hos Kong Walther lancerer vi vores store Sommer Udsalg ☀️ – og det er tid til at forkæle dig selv med elegante styles, holdbart design og uimodståelige priser! ✨ Hvorfor dig klar til at stråle på stranden i moderne badetøj, udforske et</textarea>

                            <div class="character-count">
                                <span class="count-text">389 / 2200</span>
                            </div>

                            <button class="btn-improve">
                                <i class="fas fa-magic"></i>
                                Improve
                            </button>
                        </div>

                        <div class="form-section">
                            <label class="form-label">Media</label>
                            <p class="media-requirement">You need at least 1 media item(s) to continue</p>

                            <div class="media-buttons">
                                <button class="btn-media btn-upload">
                                    <i class="fas fa-upload"></i>
                                    Upload
                                </button>
                                <button class="btn-media btn-ai">
                                    <i class="fas fa-sparkles"></i>
                                    AI
                                </button>
                                <button class="btn-media btn-stock">
                                    <i class="fas fa-search"></i>
                                    Stock
                                </button>
                                <button class="btn-media btn-library">
                                    <i class="fas fa-folder"></i>
                                    Library
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Preview -->
                <div class="col-lg-6">
                    <div class="preview-card">
                        <div class="preview-header">
                            <div class="preview-user">
                                <div class="user-avatar"></div>
                                <span class="user-name">Kong Walther</span>
                            </div>
                            <button class="btn-more">
                                <i class="fas fa-ellipsis-h"></i>
                            </button>
                        </div>

                        <div class="preview-image-container">
                            <button class="carousel-btn carousel-prev">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <div class="preview-image">
                                <!-- Preview image placeholder -->
                            </div>
                            <button class="carousel-btn carousel-next">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>

                        <div class="preview-actions">
                            <div class="action-icons">
                                <button class="action-btn">
                                    <i class="far fa-heart"></i>
                                </button>
                                <button class="action-btn">
                                    <i class="far fa-comment"></i>
                                </button>
                                <button class="action-btn">
                                    <i class="far fa-paper-plane"></i>
                                </button>
                            </div>
                            <button class="action-btn">
                                <i class="far fa-bookmark"></i>
                            </button>
                        </div>

                        <div class="preview-caption">
                            Drømmer du om at opgradere din sommergarderoben uden at sprænge budgettet? Så har vi godt
                            nyt til dig! Hos Kong Walther lancerer vi vores store Sommer Udsalg ☀️ – og det er tid til
                            at forkæle dig selv med elegante styles, holdbart design og uimodståelige priser! ✨ Hvorfor
                            dig klar til at stråle på stranden i moderne badetøj, udforske et
                        </div>

                        <button class="btn-schedule">
                            Go to scheduling
                        </button>

                        <button class="btn-chat">
                            <i class="fas fa-comments"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.js" type="text/javascript"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
    <!-- Custom JS -->
    <script src="{{ asset('js/admin/script.js') }}"></script>
    @stack('scripts')
    <script>
        // Refresh button functionality
        document.getElementById('refresh').addEventListener('click', function() {
            location.reload();
        });
    </script>
</body>

</html>
