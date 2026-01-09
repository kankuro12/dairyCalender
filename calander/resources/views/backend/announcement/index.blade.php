<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Announcements</title>
	<style>
		*, *::before, *::after { box-sizing: border-box; }
		:root { color-scheme: light; }

		body {
			font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
			background: #f7f7fb;
			color: #111827;
			margin: 0;
			padding: 24px;
		}

		.container {
			max-width: 980px;
			margin: 0 auto;
			background: #fff;
			border: 1px solid #e5e7eb;
			border-radius: 10px;
			padding: 20px;
		}

		h1 {
			margin: 0 0 16px;
			font-size: 22px;
		}

		.grid {
			display: grid;
			grid-template-columns: 1fr 1fr;
			gap: 16px;
		}

		.grid > * { min-width: 0; }

		.section {
			border: 1px solid #e5e7eb;
			border-radius: 10px;
			padding: 16px;
			background: #ffffff;
		}

		.section-title {
			font-weight: 700;
			margin: 0 0 12px;
			font-size: 14px;
			color: #374151;
			text-transform: uppercase;
			letter-spacing: 0.04em;
		}

		.field { margin-bottom: 12px; }
		label {
			display: block;
			font-size: 13px;
			color: #374151;
			margin-bottom: 6px;
		}

		input[type="text"],
		input[type="number"],
		input[type="datetime-local"],
		select {
			width: 100%;
			padding: 10px 12px;
			border: 1px solid #d1d5db;
			border-radius: 8px;
			font-size: 14px;
			outline: none;
			max-width: 100%;
			background: #fff;
		}

		input[type="text"]:focus,
		input[type="number"]:focus,
		input[type="datetime-local"]:focus,
		select:focus {
			border-color: #9ca3af;
		}

		.actions {
			display: flex;
			gap: 10px;
			margin-top: 16px;
			align-items: center;
			flex-wrap: wrap;
		}

		button {
			padding: 10px 14px;
			border: none;
			border-radius: 8px;
			background: #111827;
			color: #fff;
			cursor: pointer;
			font-size: 14px;
		}

		.btn-lite {
			background: #fff;
			color: #111827;
			border: 1px solid #d1d5db;
			padding: 7px 10px;
			border-radius: 8px;
			font-size: 12px;
			cursor: pointer;
			text-decoration: none;
			display: inline-flex;
			align-items: center;
			gap: 6px;
		}

		.btn-danger {
			background: #fff;
			color: #991b1b;
			border: 1px solid #fecaca;
		}

		.msg {
			padding: 10px 12px;
			border-radius: 8px;
			margin: 0 0 16px;
			font-size: 14px;
		}

		.msg-success { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; }
		.msg-error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }

		.error-text { font-size: 12px; color: #991b1b; margin-top: 6px; }

		.table-wrap { overflow: auto; border-radius: 10px; border: 1px solid #e5e7eb; }
		table { width: 100%; border-collapse: collapse; min-width: 760px; background: #fff; }
		thead th {
			text-align: left;
			font-size: 12px;
			color: #374151;
			text-transform: uppercase;
			letter-spacing: 0.04em;
			padding: 10px 12px;
			border-bottom: 1px solid #e5e7eb;
			background: #fafafa;
		}
		tbody td {
			padding: 10px 12px;
			border-bottom: 1px solid #f3f4f6;
			vertical-align: top;
			font-size: 14px;
			color: #111827;
		}
		.badge {
			display: inline-block;
			padding: 3px 8px;
			border-radius: 999px;
			font-size: 12px;
			border: 1px solid #e5e7eb;
			background: #fafafa;
			color: #374151;
			white-space: nowrap;
		}

		.muted { color: #6b7280; font-size: 12px; }

		@media (max-width: 820px) {
			.grid { grid-template-columns: 1fr; }
		}
	</style>
</head>
<body>
	<div class="container">
		<h1>Announcements</h1>

		@if (session('success'))
			<div class="msg msg-success">{{ session('success') }}</div>
		@endif

		@if ($errors->any())
			<div class="msg msg-error">Please fix the errors and try again.</div>
		@endif

		@php
			$editing = $editing ?? null;
			$announcements = $announcements ?? collect();

			$value = function (string $key, $fallback = '') use ($editing) {
				if (old($key) !== null) return old($key);
				if ($editing && isset($editing->{$key})) return $editing->{$key};
				return $fallback;
			};

			$dtLocal = function ($dt): string {
				if (!$dt) return '';
				try {
					return \Carbon\Carbon::parse($dt)->format('Y-m-d\\TH:i');
				} catch (\Throwable $e) {
					return '';
				}
			};
		@endphp

		<div class="grid">
			<div class="section">
				<div class="section-title">{{ $editing ? 'Edit Announcement' : 'Add Announcement' }}</div>

				<form action="{{ $editing ? route('announcements.update', $editing) : route('announcements.store') }}" method="POST">
					@csrf
					@if ($editing)
						@method('PUT')
					@endif

					<div class="field">
						<label for="title">Title</label>
						<input id="title" type="text" name="title" value="{{ $value('title') }}" placeholder="Title">
						@error('title')<div class="error-text">{{ $message }}</div>@enderror
					</div>

					<div class="field">
						<label for="nep_title">Nepali Title</label>
						<input id="nep_title" type="text" name="nep_title" value="{{ $value('nep_title') }}" placeholder="Nepali title (optional)">
						@error('nep_title')<div class="error-text">{{ $message }}</div>@enderror
					</div>

					<div class="field">
						<label for="type">Type</label>
						<select id="type" name="type">
							@php $type = (string) $value('type', 'announcement'); @endphp
							<option value="news" @selected($type === 'news')>News</option>
							<option value="announcement" @selected($type === 'announcement')>Announcement</option>
							<option value="event" @selected($type === 'event')>Event</option>
							<option value="alert" @selected($type === 'alert')>Alert</option>
						</select>
						@error('type')<div class="error-text">{{ $message }}</div>@enderror
					</div>

					<div class="field">
						<label for="priority">Priority</label>
						<input id="priority" type="number" name="priority" value="{{ $value('priority', 0) }}" placeholder="0">
						@error('priority')<div class="error-text">{{ $message }}</div>@enderror
					</div>

					<div class="field">
						<label for="status">Status</label>
						@php
							$statusVal = $value('status', true);
							$statusBool = filter_var($statusVal, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
							if ($statusBool === null) $statusBool = (bool) $statusVal;
						@endphp
						<select id="status" name="status">
							<option value="1" @selected($statusBool === true)>Active</option>
							<option value="0" @selected($statusBool === false)>Inactive</option>
						</select>
						@error('status')<div class="error-text">{{ $message }}</div>@enderror
					</div>

					<div class="field">
						<label for="start_at">Start At</label>
						<input id="start_at" type="datetime-local" name="start_at" value="{{ $dtLocal($value('start_at')) }}">
						@error('start_at')<div class="error-text">{{ $message }}</div>@enderror
					</div>

					<div class="field">
						<label for="end_at">End At</label>
						<input id="end_at" type="datetime-local" name="end_at" value="{{ $dtLocal($value('end_at')) }}">
						@error('end_at')<div class="error-text">{{ $message }}</div>@enderror
					</div>

					<div class="actions">
						<button type="submit">{{ $editing ? 'Update' : 'Create' }}</button>
						@if ($editing)
							<a class="btn-lite" href="{{ route('announcements.index') }}">Cancel</a>
						@endif
					</div>
				</form>
			</div>

			<div class="section">
				<div class="section-title">Existing</div>
				<div class="muted">Cache key used on frontend: <strong>announcement_bar</strong> (auto cleared on create/update/delete)</div>
				<div style="height: 12px;"></div>
				<div class="table-wrap">
					<table>
						<thead>
							<tr>
								<th>Title</th>
								<th>Type</th>
								<th>Priority</th>
								<th>Status</th>
								<th>Start</th>
								<th>End</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@forelse ($announcements as $row)
								<tr>
									<td>
										<div style="font-weight: 600;">{{ $row->title }}</div>
										@if (!empty($row->nep_title))
											<div class="muted">{{ $row->nep_title }}</div>
										@endif
									</td>
									<td><span class="badge">{{ $row->type }}</span></td>
									<td>{{ (int) $row->priority }}</td>
									<td>
										@if ($row->status)
											<span class="badge">Active</span>
										@else
											<span class="badge">Inactive</span>
										@endif
									</td>
									<td class="muted">{{ $row->start_at ? \Carbon\Carbon::parse($row->start_at)->format('Y-m-d H:i') : '-' }}</td>
									<td class="muted">{{ $row->end_at ? \Carbon\Carbon::parse($row->end_at)->format('Y-m-d H:i') : '-' }}</td>
									<td>
										<div class="actions" style="margin-top: 0;">
											<a class="btn-lite" href="{{ route('announcements.index', ['edit' => $row->id]) }}">Edit</a>
											<form action="{{ route('announcements.destroy', $row) }}" method="POST" onsubmit="return confirm('Delete this announcement?');">
												@csrf
												@method('DELETE')
												<button type="submit" class="btn-lite btn-danger">Delete</button>
											</form>
										</div>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="7" class="muted">No announcements yet.</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
