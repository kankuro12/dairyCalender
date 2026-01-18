@extends('backend.layout.app')
@section('content')
    <div class="content-wrapper">
        <div class="dashboard-container">
            <!-- Page Header -->
            <div class="dashboard-header">
                <div>
                    <h1 class="page-title">News Management</h1>
                    <p class="page-subtitle">Create and manage news for the slider</p>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    Please fix the errors and try again.
                </div>
            @endif

            @php
                $editing = $editing ?? null;
                $news = $news ?? collect();

                $value = function (string $key, $fallback = '') use ($editing) {
                    if (old($key) !== null) {
                        return old($key);
                    }
                    if ($editing && isset($editing->{$key})) {
                        return $editing->{$key};
                    }
                    return $fallback;
                };

                $dtLocal = function ($dt): string {
                    if (!$dt) {
                        return '';
                    }
                    try {
                        return \Carbon\Carbon::parse($dt)->format('Y-m-d\\TH:i');
                    } catch (\Throwable $e) {
                        return '';
                    }
                };
            @endphp

            <!-- News Form -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">{{ $editing ? 'Edit News' : 'Add New News' }}</h2>
                </div>
                <div class="card-body">
                    <form action="{{ $editing ? route('admin.news.update', $editing->id) : route('admin.news.store') }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @if ($editing)
                            @method('PUT')
                        @endif

                        <div class="form-grid">
                            <!-- Title (English) -->
                            <div class="form-group">
                                <label for="title" class="required">Title (English)</label>
                                <input type="text" id="title" name="title" class="form-control"
                                    value="{{ $value('title') }}" required>
                                @error('title')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Title (Nepali) -->
                            <div class="form-group">
                                <label for="title_nep">Title (Nepali)</label>
                                <input type="text" id="title_nep" name="title_nep" class="form-control"
                                    value="{{ $value('title_nep') }}">
                                @error('title_nep')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Excerpt (English) -->
                        <div class="form-group">
                            <label for="excerpt">Excerpt (English)</label>
                            <textarea id="excerpt" name="excerpt" class="form-control" rows="3">{{ $value('excerpt') }}</textarea>
                            @error('excerpt')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Excerpt (Nepali) -->
                        <div class="form-group">
                            <label for="excerpt_nep">Excerpt (Nepali)</label>
                            <textarea id="excerpt_nep" name="excerpt_nep" class="form-control" rows="3">{{ $value('excerpt_nep') }}</textarea>
                            @error('excerpt_nep')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Content (English) -->
                        <div class="form-group">
                            <label for="content">Content (English)</label>
                            <textarea id="content" name="content" class="form-control" rows="6">{{ $value('content') }}</textarea>
                            @error('content')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Content (Nepali) -->
                        <div class="form-group">
                            <label for="content_nep">Content (Nepali)</label>
                            <textarea id="content_nep" name="content_nep" class="form-control" rows="6">{{ $value('content_nep') }}</textarea>
                            @error('content_nep')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-grid">
                            <!-- Image Upload -->
                            <div class="form-group">
                                <label for="image">Image</label>
                                @if ($editing && $editing->image)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $editing->image) }}" alt="Current Image"
                                            style="max-width: 200px; max-height: 150px; border-radius: 8px;">
                                        <div class="mt-2">
                                            <label class="checkbox-label">
                                                <input type="checkbox" name="remove_image" value="1">
                                                Remove current image
                                            </label>
                                        </div>
                                    </div>
                                @endif
                                <input type="file" id="image" name="image" class="form-control" accept="image/*">
                                <small class="form-text">Recommended: 500x400px, max 2MB</small>
                                @error('image')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Source URL -->
                            <div class="form-group">
                                <label for="source_url">Source URL</label>
                                <input type="url" id="source_url" name="source_url" class="form-control"
                                    value="{{ $value('source_url') }}">
                                @error('source_url')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-grid">
                            <!-- Source Name -->
                            <div class="form-group">
                                <label for="source_name">Source Name</label>
                                <input type="text" id="source_name" name="source_name" class="form-control"
                                    value="{{ $value('source_name') }}">
                                @error('source_name')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Published At -->
                            <div class="form-group">
                                <label for="published_at">Published At</label>
                                <input type="datetime-local" id="published_at" name="published_at" class="form-control"
                                    value="{{ $dtLocal($value('published_at')) }}">
                                <small class="form-text">Leave empty to publish immediately</small>
                                @error('published_at')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-grid">
                            <!-- Priority -->
                            <div class="form-group">
                                <label for="priority">Priority</label>
                                <input type="number" id="priority" name="priority" class="form-control"
                                    value="{{ $value('priority', 0) }}" min="0">
                                <small class="form-text">Higher number = higher priority</small>
                                @error('priority')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="form-group">
                                <label for="status">Status</label>
                                <label class="checkbox-label">
                                    <input type="checkbox" id="status" name="status" value="1"
                                        {{ $value('status', true) ? 'checked' : '' }}>
                                    Active
                                </label>
                            </div>
                        </div>

                        <!-- Sources JSON (for multiple sources with icons) -->
                        <div class="form-group">
                            <label for="sources_json">Sources JSON (Advanced)</label>
                            <textarea id="sources_json" name="sources_json" class="form-control" rows="3"
                                placeholder='[{"name":"Twitter","icon":"🐦","color":"#1DA1F2"},{"name":"YouTube","icon":"📺","color":"#FF0000"}]'>{{ $editing && $editing->sources ? json_encode($editing->sources) : '' }}</textarea>
                            <small class="form-text">JSON array of source objects with name, icon, and color</small>
                            @error('sources_json')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                {{ $editing ? 'Update News' : 'Create News' }}
                            </button>
                            @if ($editing)
                                <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">
                                    Cancel Edit
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Fetch from API Form -->
            <div class="card mt-4">
                <div class="card-header">
                    <h2 class="card-title">Fetch News from API</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.news.fetch-api') }}" method="POST">
                        @csrf
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="api_url" class="required">API URL</label>
                                <input type="url" id="api_url" name="api_url" class="form-control"
                                    placeholder="https://api.example.com/news" required>
                                @error('api_url')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                                @error('api_error')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="api_key">API Key (Optional)</label>
                                <input type="text" id="api_key" name="api_key" class="form-control"
                                    placeholder="Bearer token">
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-download"></i>
                                Fetch from API
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- News List -->
            <div class="card mt-4">
                <div class="card-header">
                    <h2 class="card-title">All News ({{ $news->total() }})</h2>
                </div>
                <div class="card-body">
                    @if ($news->count() > 0)
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Title</th>
                                        <th>Source</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Published</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($news as $item)
                                        <tr>
                                            <td>
                                                @if ($item->image)
                                                    <img src="{{ asset('storage/' . $item->image) }}" alt="News"
                                                        style="width: 60px; height: 45px; object-fit: cover; border-radius: 4px;">
                                                @else
                                                    <div
                                                        style="width: 60px; height: 45px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-image" style="color: #ccc;"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div style="max-width: 300px;">
                                                    <strong>{{ Str::limit($item->title, 50) }}</strong>
                                                    @if ($item->title_nep)
                                                        <br><small
                                                            style="color: #666;">{{ Str::limit($item->title_nep, 50) }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $item->source_type }}</span>
                                                @if ($item->source_name)
                                                    <br><small>{{ $item->source_name }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $item->priority }}</td>
                                            <td>
                                                <span
                                                    class="badge {{ $item->status ? 'badge-success' : 'badge-danger' }}">
                                                    {{ $item->status ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($item->published_at)
                                                    {{ $item->published_at->format('Y-m-d H:i') }}
                                                @else
                                                    <span class="text-muted">Not set</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="{{ route('admin.news.index', ['edit' => $item->id]) }}"
                                                        class="btn btn-sm btn-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.news.destroy', $item->id) }}"
                                                        method="POST" style="display: inline;"
                                                        onsubmit="return confirm('Are you sure you want to delete this news?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $news->links() }}
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-newspaper"></i>
                            <p>No news articles found. Create your first one above!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
