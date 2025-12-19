@if (session('success'))
    <div
        style="padding:12px 16px;background:#ecfdf5;border:1px solid #bbf7d0;color:#065f46;border-radius:6px;margin:12px 0;">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div
        style="padding:12px 16px;background:#fff1f2;border:1px solid #fecaca;color:#991b1b;border-radius:6px;margin:12px 0;">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div
        style="padding:12px 16px;background:#fff7ed;border:1px solid #fee2b3;color:#92400e;border-radius:6px;margin:12px 0;">
        <ul style="margin:0;padding-left:18px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
