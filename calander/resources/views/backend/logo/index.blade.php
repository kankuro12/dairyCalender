<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Settings</title>
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
        input[type="email"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            max-width: 100%;
        }

        input[type="color"] {
            width: 100%;
            height: 42px;
            padding: 6px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #fff;
        }

        input[type="text"]:focus,
        input[type="email"]:focus {
            border-color: #9ca3af;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 16px;
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

        .msg {
            padding: 10px 12px;
            border-radius: 8px;
            margin: 0 0 16px;
            font-size: 14px;
        }

        .msg-success { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; }
        .msg-error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }

        /* Custom Dropify */
        .dropify {
            border: 2px dashed #d1d5db;
            border-radius: 10px;
            padding: 12px;
            background: #fafafa;
            transition: border-color 0.15s ease, background 0.15s ease;
            position: relative;
        }
        .dropify.is-dragover {
            border-color: #6b7280;
            background: #f3f4f6;
        }

        .dropify .dropify-input {
            position: absolute;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
        }

        .dropify-inner {
            display: grid;
            grid-template-columns: minmax(0, 120px) minmax(0, 1fr);
            gap: 12px;
            align-items: center;
        }

        .dropify-preview {
            width: 120px;
            height: 80px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .dropify-preview img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        .dropify-meta {
            min-height: 80px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 6px;
            min-width: 0;
        }

        .dropify-title {
            font-weight: 600;
            font-size: 14px;
        }

        .dropify-hint {
            font-size: 12px;
            color: #6b7280;
        }

        .dropify-controls {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-lite {
            background: #fff;
            color: #111827;
            border: 1px solid #d1d5db;
            padding: 7px 10px;
            border-radius: 8px;
            font-size: 12px;
            cursor: pointer;
        }

        .btn-danger {
            background: #fff;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .error-text { font-size: 12px; color: #991b1b; margin-top: 6px; }

        @media (max-width: 820px) {
            .grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 520px) {
            .dropify-inner { grid-template-columns: 1fr; }
            .dropify-preview { width: 100%; height: 140px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Settings</h1>

        @if (session('success'))
            <div class="msg msg-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="msg msg-error">Please fix the errors and try again.</div>
        @endif

        @php
            $settings = $settings ?? [];
            $sliders = $sliders ?? [];

            $get = function (string $key, string $fallback = '') use ($settings) {
                return old($key, $settings[$key] ?? $fallback);
            };
            $imageUrl = function (string $key) use ($settings) {
                $path = $settings[$key] ?? null;
                if (!$path) return '';
                return asset('storage/' . ltrim($path, '/'));
            };
            $sliderInitialUrl = function (string $key) use ($sliders) {
                $path = $sliders[$key] ?? null;
                if (!$path) return '';
                return asset('storage/' . ltrim($path, '/'));
            };
        @endphp

        <form action="{{ route('events.logo.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid">
                <div class="section">
                    <div class="section-title">Basic Info</div>

                    <div class="field">
                        <label for="site_name">Name</label>
                        <input id="site_name" type="text" name="site_name" value="{{ $get('site_name') }}" placeholder="Website / Company name">
                        @error('site_name')<div class="error-text">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="contact_phone">Contact Phone</label>
                        <input id="contact_phone" type="text" name="contact_phone" value="{{ $get('contact_phone') }}" placeholder="Phone number">
                        @error('contact_phone')<div class="error-text">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="contact_email">Contact Email</label>
                        <input id="contact_email" type="email" name="contact_email" value="{{ $get('contact_email') }}" placeholder="Email address">
                        @error('contact_email')<div class="error-text">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="contact_address">Contact Address</label>
                        <input id="contact_address" type="text" name="contact_address" value="{{ $get('contact_address') }}" placeholder="Address">
                        @error('contact_address')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="section">
                    <div class="section-title">Logo</div>

                    <div class="field">
                        <label for="logo_color">Logo Color</label>
                        <input id="logo_color" type="color" name="logo_color" value="{{ $get('logo_color', '#000000') }}">
                        @error('logo_color')<div class="error-text">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="logo_color_hex">Logo Color (Hex)</label>
                        <input id="logo_color_hex" type="text" name="logo_color_hex" value="{{ $get('logo_color_hex', $get('logo_color', '#000000')) }}" placeholder="#000000">
                        @error('logo_color_hex')<div class="error-text">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label>Logo Image</label>
                        <input type="hidden" name="remove_logo_image" value="0">
                        <div class="dropify" data-initial-url="{{ $imageUrl('logo_image') }}">
                            <input class="dropify-input js-dropify" type="file" name="logo_image" accept="image/*" data-remove-target="remove_logo_image">
                            <div class="dropify-inner">
                                <div class="dropify-preview" aria-hidden="true">
                                    <img alt="" />
                                </div>
                                <div class="dropify-meta">
                                    <div class="dropify-title">Drop an image here or click to upload</div>
                                    <div class="dropify-hint">Allowed: JPG/PNG/WEBP up to 4MB</div>
                                    <div class="dropify-controls">
                                        <button type="button" class="btn-lite js-dropify-browse">Choose file</button>
                                        <button type="button" class="btn-lite btn-danger js-dropify-clear">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @error('logo_image')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="section" style="margin-top: 16px;">
                <div class="section-title">Ad Sliders</div>

                <div class="actions" style="margin-top: 0; margin-bottom: 12px;">
                    <button type="button" class="btn-lite" id="addSliderBtn">Add Slider</button>
                </div>

                <div class="grid" id="slidersGrid">
                    @php
                        $existingSliderNumbers = [];
                        foreach (($sliders ?? []) as $k => $v) {
                            if (preg_match('/^slider(\d+)$/', (string) $k, $m)) {
                                $existingSliderNumbers[] = (int) $m[1];
                            }
                        }
                        $existingSliderNumbers = array_values(array_unique($existingSliderNumbers));
                        sort($existingSliderNumbers);
                    @endphp

                    @forelse ($existingSliderNumbers as $n)
                        @php $key = 'slider' . $n; @endphp
                        <div class="field js-slider-field" data-slider-number="{{ $n }}">
                            <label>Slider {{ $n }}</label>
                            <input type="hidden" name="remove_{{ $key }}" value="0">
                            <div class="dropify" data-initial-url="{{ $sliderInitialUrl($key) }}">
                                <input class="dropify-input js-dropify" type="file" name="{{ $key }}" accept="image/*" data-remove-target="remove_{{ $key }}">
                                <div class="dropify-inner">
                                    <div class="dropify-preview" aria-hidden="true"><img alt="" /></div>
                                    <div class="dropify-meta">
                                        <div class="dropify-title">Drop an image here or click to upload</div>
                                        <div class="dropify-hint">Allowed: JPG/PNG/WEBP up to 4MB</div>
                                        <div class="dropify-controls">
                                            <button type="button" class="btn-lite js-dropify-browse">Choose file</button>
                                            <button type="button" class="btn-lite btn-danger js-dropify-clear">Remove</button>
                                            <button type="button" class="btn-lite btn-danger js-slider-remove">Remove field</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @error($key)<div class="error-text">{{ $message }}</div>@enderror
                        </div>
                    @empty
                        {{-- No sliders yet. Admin can add using the button. --}}
                    @endforelse
                </div>
            </div>

            <div class="actions">
                <button type="submit">Save Settings</button>
            </div>
        </form>
    </div>

    <script>
        (function () {
            function initDropify(wrapper) {
                const fileInput = wrapper.querySelector('input[type="file"].js-dropify');
                if (!fileInput) return;

                const initialUrl = wrapper.getAttribute('data-initial-url') || '';
                setPreview(wrapper, initialUrl);

                const browseBtn = wrapper.querySelector('.js-dropify-browse');
                const clearBtn = wrapper.querySelector('.js-dropify-clear');
                const removeTarget = fileInput.getAttribute('data-remove-target');
                const removeHidden = removeTarget ? document.querySelector('input[name="' + removeTarget + '"]') : null;

                if (browseBtn) {
                    browseBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        fileInput.click();
                    });
                }

                // Click anywhere on the dropify (except controls) to browse
                wrapper.addEventListener('click', function (e) {
                    if (e.target && e.target.closest && e.target.closest('.dropify-controls')) return;
                    // ignore if clicking an input/label/button
                    if (e.target && (e.target.tagName === 'BUTTON' || e.target.tagName === 'INPUT' || e.target.tagName === 'LABEL')) return;
                    fileInput.click();
                });

                if (clearBtn) {
                    clearBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        clearInput(fileInput);
                        setPreview(wrapper, '');
                        if (removeHidden) removeHidden.value = '1';
                    });
                }

                fileInput.addEventListener('change', function () {
                    if (removeHidden) removeHidden.value = '0';
                    const file = fileInput.files && fileInput.files[0];
                    if (!file) {
                        setPreview(wrapper, initialUrl);
                        return;
                    }
                    const url = URL.createObjectURL(file);
                    setPreview(wrapper, url);
                });

                // Drag & drop
                ['dragenter', 'dragover'].forEach(function (evt) {
                    wrapper.addEventListener(evt, function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        wrapper.classList.add('is-dragover');
                    });
                });

                ['dragleave', 'drop'].forEach(function (evt) {
                    wrapper.addEventListener(evt, function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        wrapper.classList.remove('is-dragover');
                    });
                });

                wrapper.addEventListener('drop', function (e) {
                    const dt = e.dataTransfer;
                    if (!dt || !dt.files || !dt.files.length) return;
                    const file = dt.files[0];
                    if (!file.type || !file.type.startsWith('image/')) return;

                    const transfer = new DataTransfer();
                    transfer.items.add(file);
                    fileInput.files = transfer.files;

                    if (removeHidden) removeHidden.value = '0';
                    const url = URL.createObjectURL(file);
                    setPreview(wrapper, url);
                });
            }

            function setPreview(wrapper, url) {
                const img = wrapper.querySelector('.dropify-preview img');
                if (!img) return;
                if (url) {
                    img.src = url;
                    img.style.display = 'block';
                } else {
                    img.removeAttribute('src');
                    img.style.display = 'none';
                }
            }

            function clearInput(fileInput) {
                try { fileInput.value = ''; } catch (e) {}
            }


            // Init all existing dropify widgets
            document.querySelectorAll('.dropify').forEach(function (wrapper) {
                initDropify(wrapper);
            });

            function getNextSliderNumber() {
                const numbers = Array.from(document.querySelectorAll('.js-slider-field'))
                    .map(function (el) { return parseInt(el.getAttribute('data-slider-number') || '0', 10); })
                    .filter(function (n) { return !isNaN(n) && n > 0; });
                const max = numbers.length ? Math.max.apply(null, numbers) : 0;
                return max + 1;
            }

            function createSliderField(n) {
                const key = 'slider' + n;
                const wrapper = document.createElement('div');
                wrapper.className = 'field js-slider-field';
                wrapper.setAttribute('data-slider-number', String(n));

                wrapper.innerHTML = '' +
                    '<label>Slider ' + n + '</label>' +
                    '<input type="hidden" name="remove_' + key + '" value="0">' +
                    '<div class="dropify" data-initial-url="">' +
                    '  <input class="dropify-input js-dropify" type="file" name="' + key + '" accept="image/*" data-remove-target="remove_' + key + '">' +
                    '  <div class="dropify-inner">' +
                    '    <div class="dropify-preview" aria-hidden="true"><img alt="" /></div>' +
                    '    <div class="dropify-meta">' +
                    '      <div class="dropify-title">Drop an image here or click to upload</div>' +
                    '      <div class="dropify-hint">Allowed: JPG/PNG/WEBP up to 4MB</div>' +
                    '      <div class="dropify-controls">' +
                    '        <button type="button" class="btn-lite js-dropify-browse">Choose file</button>' +
                    '        <button type="button" class="btn-lite btn-danger js-dropify-clear">Remove</button>' +
                    '        <button type="button" class="btn-lite btn-danger js-slider-remove">Remove field</button>' +
                    '      </div>' +
                    '    </div>' +
                    '  </div>' +
                    '</div>';

                const dropify = wrapper.querySelector('.dropify');
                if (dropify) initDropify(dropify);

                return wrapper;
            }

            const addBtn = document.getElementById('addSliderBtn');
            const slidersGrid = document.getElementById('slidersGrid');

            if (addBtn && slidersGrid) {
                addBtn.addEventListener('click', function () {
                    const n = getNextSliderNumber();
                    const field = createSliderField(n);
                    slidersGrid.appendChild(field);
                });
            }

            // Remove slider field behavior
            document.addEventListener('click', function (e) {
                const btn = e.target && e.target.closest ? e.target.closest('.js-slider-remove') : null;
                if (!btn) return;
                e.preventDefault();
                e.stopPropagation();

                const field = btn.closest('.js-slider-field');
                if (!field) return;

                // If it maps to a saved key, mark remove_key=1 so DB deletes it
                const number = parseInt(field.getAttribute('data-slider-number') || '0', 10);
                const key = 'slider' + number;
                const removeHidden = field.querySelector('input[name="remove_' + key + '"]');
                const fileInput = field.querySelector('input[type="file"][name="' + key + '"]');
                const hasInitial = !!(field.querySelector('.dropify') && field.querySelector('.dropify').getAttribute('data-initial-url'));
                const hasSelected = !!(fileInput && fileInput.files && fileInput.files.length);

                if (hasInitial || hasSelected) {
                    if (removeHidden) removeHidden.value = '1';
                    if (fileInput) clearInput(fileInput);
                    const dropify = field.querySelector('.dropify');
                    if (dropify) setPreview(dropify, '');
                    field.style.display = 'none';
                } else {
                    field.remove();
                }
            });

            // Sync logo color picker <-> hex input
            (function syncLogoColor() {
                const picker = document.getElementById('logo_color');
                const hex = document.getElementById('logo_color_hex');
                if (!picker || !hex) return;

                function normalize(v) {
                    if (!v) return '';
                    v = String(v).trim();
                    if (!v.startsWith('#')) v = '#' + v;
                    if (/^#([0-9a-fA-F]{6})$/.test(v)) return v.toLowerCase();
                    return '';
                }

                picker.addEventListener('input', function () {
                    hex.value = picker.value;
                });

                hex.addEventListener('input', function () {
                    const n = normalize(hex.value);
                    if (n) picker.value = n;
                });

                // initial normalize
                const initial = normalize(hex.value) || normalize(picker.value) || '#000000';
                picker.value = initial;
                hex.value = initial;
            })();
        })();
    </script>
</body>
</html>