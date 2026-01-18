@if ($showNewsSection && $newsItems->count() > 0)
    <div class="slider-container">
        <h2 class="slider-header">{{ __('site.Nepali') }} {{ __('site.News') }}</h2>

        <div class="slider-wrapper">
            <button type="button" class="nav-button nav-prev" id="prevBtn" aria-label="Previous">
                <span aria-hidden="true"><i class="fa-solid fa-angle-left" style="margin-left:2px "></i></span>
            </button>

            <div class="slider-track" id="sliderTrack">
                @foreach ($newsItems as $news)
                    <div class="slide-item">
                        @if ($news->image)
                            <img src="{{ asset('storage/' . $news->image) }}" alt="{{ $news->title }}">
                        @else
                            <img src="https://images.unsplash.com/photo-1464746133101-a2c3f88e0dd9?w=500&h=400&fit=crop"
                                alt="{{ $news->title }}">
                        @endif
                        <div class="slide-overlay">
                            <a href="{{ route('news.show', $news->id) }}" class="slide-link">
                                <div class="slide-title">
                                    {{ app()->getLocale() === 'np' && $news->title_nep ? $news->title_nep : $news->title }}
                                </div>
                            </a>
                            @if ($news->sources && is_array($news->sources) && count($news->sources) > 0)
                                <div class="slide-sources">
                                    @foreach (array_slice($news->sources, 0, 4) as $source)
                                        <div class="source-icon" style="background: {{ $source['color'] ?? '#666' }};"
                                            title="{{ $source['name'] ?? 'Source' }}">
                                            {{ $source['icon'] ?? '📰' }}
                                        </div>
                                    @endforeach
                                    @if (count($news->sources) > 4)
                                        <div class="source-count">+{{ count($news->sources) - 4 }}</div>
                                    @endif
                                </div>
                            @elseif($news->source_name)
                                <div class="slide-sources">
                                    <div class="source-icon" style="background: #666;">📰</div>
                                    <small style="color: white; margin-left: 5px;">{{ $news->source_name }}</small>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="button" class="nav-button nav-next" id="nextBtn" aria-label="Next">
                <span aria-hidden="true"><i class="fa-solid fa-angle-right" style="margin-right:2px "></i></span>
            </button>
        </div>
    </div>
@endif
