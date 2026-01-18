<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ app()->getLocale() === 'np' && $news->title_nep ? $news->title_nep : $news->title }} -
        {{ setting('site_name') ?? 'Calendar' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app/singleNews/index.css') }}">

</head>

<body>
    <nav class="nav">
        <!-- Logo Section -->
        <div class="logo">
            <div class="logo-icon {{ setting('logo_image') ? 'logo-icon--image' : '' }}">
                @if (setting('logo_image'))
                    <img src="{{ getLogo() }}" alt="Site Logo" class="site-logo" loading="eager" decoding="async">
                @endif
            </div>
            <a href="/" class="logo-text"
                style="color:{{ setting('logo_color') }}">{{ setting('site_name') ?? 'null' }}</a>
        </div>

        <div class="nav-right">
            <a href="/calendar" class="back-btn" style="color: {{ setting('logo_color') }}">
                <i class="fas fa-arrow-left"></i> Back to Calendar
            </a>
        </div>
    </nav>

    <div class="news-container">
        <article class="news-card">
            @if ($news->image)
                <img src="{{ asset('storage/' . $news->image) }}" alt="{{ $news->title }}" class="news-image">
            @endif

            <div class="news-content">
                <div class="news-meta">
                    @if ($news->published_at)
                        <div class="news-meta-item">
                            <i class="far fa-calendar"></i>
                            <span>{{ $news->published_at->format('F d, Y') }}</span>
                        </div>
                    @endif

                    @if ($news->source_name)
                        <div class="news-meta-item">
                            <i class="far fa-newspaper"></i>
                            <span>{{ $news->source_name }}</span>
                        </div>
                    @endif

                    <div class="news-meta-item">
                        <i class="far fa-eye"></i>
                        <span>{{ $news->source_type === 'api' ? 'API Import' : 'Manual' }}</span>
                    </div>
                </div>

                <h1 class="news-title">
                    {{ app()->getLocale() === 'np' && $news->title_nep ? $news->title_nep : $news->title }}
                </h1>

                @if (app()->getLocale() === 'np' && $news->excerpt_nep)
                    <div class="news-excerpt">{{ $news->excerpt_nep }}</div>
                @elseif($news->excerpt)
                    <div class="news-excerpt">{{ $news->excerpt }}</div>
                @endif

                <div class="news-body">
                    @if (app()->getLocale() === 'np' && $news->content_nep)
                        {!! nl2br(e($news->content_nep)) !!}
                    @elseif($news->content)
                        {!! nl2br(e($news->content)) !!}
                    @else
                        <p>Content not available.</p>
                    @endif
                </div>

                @if ($news->sources && is_array($news->sources) && count($news->sources) > 0)
                    <div class="news-sources">
                        <span class="source-label">Sources:</span>
                        @foreach ($news->sources as $source)
                            <div class="source-icon" style="background: {{ $source['color'] ?? '#666' }};"
                                title="{{ $source['name'] ?? 'Source' }}">
                                {{ $source['icon'] ?? '📰' }}
                            </div>
                        @endforeach
                    </div>
                @endif

                @if ($news->source_url)
                    <div class="news-sources">
                        <a href="{{ $news->source_url }}" target="_blank" rel="noopener noreferrer"
                            class="btn btn-primary">
                            <i class="fas fa-external-link-alt"></i> View Original Source
                        </a>
                    </div>
                @endif
            </div>
        </article>
    </div>
</body>

</html>
