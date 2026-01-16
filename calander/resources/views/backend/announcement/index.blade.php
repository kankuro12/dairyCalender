@extends('backend.layout.app')
@section('content')
    <div class="content-wrapper">
        <div class="dashboard-container">
            <!-- Page Header -->
            <div class="dashboard-header">
                <div>
                    <h1 class="page-title">Announcements Management</h1>
                    <p class="page-subtitle">Create and manage announcements for your calendar</p>
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
                $announcements = $announcements ?? collect();

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

            <!-- Form Section (Top) -->
            <div class="content-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas {{ $editing ? 'fa-edit' : 'fa-plus-circle' }}"></i>
                        {{ $editing ? 'Edit Announcement' : 'Create New Announcement' }}
                    </h2>
                </div>

                <div class="card-body">
                    <form
                        action="{{ $editing ? route('admin.announcements.update', $editing) : route('admin.announcements.store') }}"
                        method="POST" class="announcement-form-horizontal">
                        @csrf
                        @if ($editing)
                            @method('PUT')
                        @endif

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="title" class="form-label">
                                    <i class="fas fa-heading"></i> Title <span class="required">*</span>
                                </label>
                                <input id="title" type="text" name="title" value="{{ $value('title') }}"
                                    class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}"
                                    placeholder="Enter announcement title" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="nep_title" class="form-label">
                                    <i class="fas fa-language"></i> Nepali Title
                                </label>
                                <input id="nep_title" type="text" name="nep_title" value="{{ $value('nep_title') }}"
                                    class="form-control {{ $errors->has('nep_title') ? 'is-invalid' : '' }}"
                                    placeholder="नेपाली शीर्षक (वैकल्पिक)">
                                @error('nep_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="type" class="form-label">
                                    <i class="fas fa-tag"></i> Type <span class="required">*</span>
                                </label>
                                <select id="type" name="type"
                                    class="form-select {{ $errors->has('type') ? 'is-invalid' : '' }}" required>
                                    @php $type = (string) $value('type', 'announcement'); @endphp
                                    <option value="news" @selected($type === 'news')> News</option>
                                    <option value="announcement" @selected($type === 'announcement')> Announcement</option>
                                    <option value="event" @selected($type === 'event')> Event</option>
                                    <option value="alert" @selected($type === 'alert')> Alert</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="priority" class="form-label">
                                    <i class="fas fa-sort-amount-up"></i> Priority
                                </label>
                                <input id="priority" type="number" name="priority" value="{{ $value('priority', 0) }}"
                                    class="form-control {{ $errors->has('priority') ? 'is-invalid' : '' }}"
                                    placeholder="0 = Low, 10 = High" min="0" max="100">
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="status" class="form-label">
                                    <i class="fas fa-toggle-on"></i> Status
                                </label>
                                @php
                                    $statusVal = $value('status', true);
                                    $statusBool = filter_var(
                                        $statusVal,
                                        FILTER_VALIDATE_BOOLEAN,
                                        FILTER_NULL_ON_FAILURE,
                                    );
                                    if ($statusBool === null) {
                                        $statusBool = (bool) $statusVal;
                                    }
                                @endphp
                                <select id="status" name="status"
                                    class="form-select {{ $errors->has('status') ? 'is-invalid' : '' }}">
                                    <option value="1" @selected($statusBool === true)> Active</option>
                                    <option value="0" @selected($statusBool === false)> Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="start_at" class="form-label">
                                    <i class="fas fa-calendar-check"></i> Start At
                                </label>
                                <input id="start_at" type="datetime-local" name="start_at"
                                    value="{{ $dtLocal($value('start_at')) }}"
                                    class="form-control {{ $errors->has('start_at') ? 'is-invalid' : '' }}">
                                @error('start_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="end_at" class="form-label">
                                    <i class="fas fa-calendar-times"></i> End At
                                </label>
                                <input id="end_at" type="datetime-local" name="end_at"
                                    value="{{ $dtLocal($value('end_at')) }}"
                                    class="form-control {{ $errors->has('end_at') ? 'is-invalid' : '' }}">
                                @error('end_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas {{ $editing ? 'fa-save' : 'fa-plus-circle' }}"></i>
                                {{ $editing ? 'Update Announcement' : 'Create Announcement' }}
                            </button>
                            @if ($editing)
                                <a class="btn btn-secondary" href="{{ route('admin.announcements.index') }}">
                                    <i class="fas fa-times"></i>
                                    Cancel
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- List Section -->
            <div class="list-section">
                <div class="content-card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <i class="fas fa-list"></i>
                            All Announcements
                        </h2>
                        <div class="card-subtitle">
                            Cache key: <code>announcement_bar</code> (auto-cleared on changes)
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Schedule</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($announcements as $row)
                                        <tr>
                                            <td>
                                                <div class="announcement-title">{{ $row->title }}</div>
                                                @if (!empty($row->nep_title))
                                                    <div class="announcement-subtitle">{{ $row->nep_title }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $row->type }}">
                                                    {{ ucfirst($row->type) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="priority-badge">{{ (int) $row->priority }}</span>
                                            </td>
                                            <td>
                                                @if ($row->status)
                                                    <span class="status-badge status-active">
                                                        <i class="fas fa-check-circle"></i> Active
                                                    </span>
                                                @else
                                                    <span class="status-badge status-inactive">
                                                        <i class="fas fa-times-circle"></i> Inactive
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="date-range">
                                                    @if ($row->start_at)
                                                        <div class="date-item">
                                                            <i class="fas fa-calendar-check"></i>
                                                            {{ \Carbon\Carbon::parse($row->start_at)->format('M d, Y H:i') }}
                                                        </div>
                                                    @endif
                                                    @if ($row->end_at)
                                                        <div class="date-item">
                                                            <i class="fas fa-calendar-times"></i>
                                                            {{ \Carbon\Carbon::parse($row->end_at)->format('M d, Y H:i') }}
                                                        </div>
                                                    @endif
                                                    @if (!$row->start_at && !$row->end_at)
                                                        <span class="text-muted">Always visible</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="{{ route('admin.announcements.index', ['edit' => $row->id]) }}"
                                                        class="btn btn-sm btn-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.announcements.destroy', $row) }}"
                                                        method="POST" style="display: inline-block;"
                                                        onsubmit="return confirm('Delete this announcement?')">
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
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center empty-state">
                                                <i class="fas fa-inbox"></i>
                                                <p>No announcements found. Create your first one!</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
