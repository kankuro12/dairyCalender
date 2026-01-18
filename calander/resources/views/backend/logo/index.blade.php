@extends('backend.layout.app')
@section('content')
    <div class="content-wrapper">
        <div class="dashboard-container">
            <!-- Page Header -->
            <div class="dashboard-header">
                <div>
                    <h1 class="page-title">Settings & Logo Management</h1>
                    <p class="page-subtitle">Configure site settings, logos, and slider images</p>
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
                    <div>
                        <strong>Please fix the following errors:</strong>
                        <ul style="margin: 8px 0 0 20px; padding: 0;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @php
                $settings = $settings ?? [];
                $sliders = $sliders ?? [];

                $get = function (string $key, string $fallback = '') use ($settings) {
                    return old($key, $settings[$key] ?? $fallback);
                };
                $imageUrls = $imageUrls ?? [];
                $sliderInitialUrls = $sliderInitialUrls ?? [];
            @endphp

            <form action="{{ route('admin.settings.logo.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="settings-grid">
                    <!-- Basic Info Section -->
                    <div class="content-card">
                        <div class="card-header">
                            <h2 class="card-title">
                                <i class="fas fa-info-circle"></i>
                                Basic Information
                            </h2>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="site_name" class="form-label">
                                    <i class="fas fa-building"></i> Site Name
                                </label>
                                <input id="site_name" type="text" name="site_name" value="{{ $get('site_name') }}"
                                    class="form-control {{ $errors->has('site_name') ? 'is-invalid' : '' }}"
                                    placeholder="Website / Company name">
                                @error('site_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="contact_phone" class="form-label">
                                    <i class="fas fa-phone"></i> Contact Phone
                                </label>
                                <input id="contact_phone" type="text" name="contact_phone"
                                    value="{{ $get('contact_phone') }}"
                                    class="form-control {{ $errors->has('contact_phone') ? 'is-invalid' : '' }}"
                                    placeholder="Phone number">
                                @error('contact_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="contact_email" class="form-label">
                                    <i class="fas fa-envelope"></i> Contact Email
                                </label>
                                <input id="contact_email" type="email" name="contact_email"
                                    value="{{ $get('contact_email') }}"
                                    class="form-control {{ $errors->has('contact_email') ? 'is-invalid' : '' }}"
                                    placeholder="Email address">
                                @error('contact_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="contact_address" class="form-label">
                                    <i class="fas fa-map-marker-alt"></i> Contact Address
                                </label>
                                <input id="contact_address" type="text" name="contact_address"
                                    value="{{ $get('contact_address') }}"
                                    class="form-control {{ $errors->has('contact_address') ? 'is-invalid' : '' }}"
                                    placeholder="Address">
                                @error('contact_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Logo Section -->
                    <div class="content-card">
                        <div class="card-header">
                            <h2 class="card-title">
                                <i class="fas fa-palette"></i>
                                Logo Settings
                            </h2>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="logo_color" class="form-label">
                                    <i class="fas fa-fill-drip"></i> Logo Color
                                </label>
                                <input id="logo_color" type="color" name="logo_color"
                                    value="{{ $get('logo_color', '#000000') }}"
                                    class="form-control form-control-color {{ $errors->has('logo_color') ? 'is-invalid' : '' }}">
                                @error('logo_color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="logo_color_hex" class="form-label">
                                    <i class="fas fa-hashtag"></i> Logo Color (Hex)
                                </label>
                                <input id="logo_color_hex" type="text" name="logo_color_hex"
                                    value="{{ $get('logo_color_hex', $get('logo_color', '#000000')) }}"
                                    class="form-control {{ $errors->has('logo_color_hex') ? 'is-invalid' : '' }}"
                                    placeholder="#000000">
                                @error('logo_color_hex')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-image"></i> Logo Image
                                </label>
                                <input type="hidden" name="remove_logo_image" value="0">
                                <div class="dropify dropify-logo" data-initial-url="{{ $imageUrls['logo_image'] ?? '' }}">
                                    <input class="dropify-input js-dropify" type="file" name="logo_image"
                                        accept="image/*" data-remove-target="remove_logo_image">
                                    <div class="dropify-inner">
                                        <div class="dropify-preview" aria-hidden="true">
                                            <img alt="" />
                                        </div>
                                        <div class="dropify-meta">
                                            <div class="dropify-title">Drop an image here or click to upload</div>
                                            <div class="dropify-hint">Allowed: JPG/PNG/WEBP up to 2MB</div>
                                            <div class="dropify-controls">
                                                <button type="button"
                                                    class="btn btn-sm btn-secondary js-dropify-browse">Choose file</button>
                                                <button type="button"
                                                    class="btn btn-sm btn-danger js-dropify-clear">Remove</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @error('logo_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Features Section -->
                    <div class="content-card">
                        <div class="card-header">
                            <h2 class="card-title">
                                <i class="fas fa-toggle-on"></i>
                                Feature Toggles
                            </h2>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-newspaper"></i> News Section
                                </label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="show_news_section"
                                        name="show_news_section" value="1"
                                        {{ old('show_news_section', $get('show_news_section', '1')) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_news_section">
                                        Show news slider section on homepage
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    When enabled, news slider will be displayed on the calendar homepage
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Ad Sliders Section -->
                    <div class="content-card" style="grid-column: 1 / -1;">
                        <div class="card-header">
                            <h2 class="card-title">
                                <i class="fas fa-images"></i>
                                Ad Sliders
                            </h2>
                            <div style="display: flex; gap: 8px; align-items: center;">
                                <button type="button" class="btn btn-info btn-sm" id="sliderInfoBtn"
                                    title="Show slider information">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                                <button type="button" class="btn btn-primary btn-sm" id="addSliderBtn">
                                    <i class="fas fa-plus"></i> Add Slider
                                </button>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="sliders-grid" id="slidersGrid">
                                @php
                                    $existingSliderNumbers = [];
                                    foreach ($sliders ?? [] as $k => $v) {
                                        if (preg_match('/^slider(\d+)$/', (string) $k, $m)) {
                                            $existingSliderNumbers[] = (int) $m[1];
                                        }
                                    }
                                    $existingSliderNumbers = array_values(array_unique($existingSliderNumbers));
                                    sort($existingSliderNumbers);
                                @endphp

                                @forelse ($existingSliderNumbers as $n)
                                    @php $key = 'slider' . $n; @endphp
                                    <div class="slider-item js-slider-field" data-slider-number="{{ $n }}">
                                        <div class="slider-header">
                                            <span class="slider-label">
                                                <i class="fas fa-image"></i> Slider {{ $n }}
                                            </span>
                                            <button type="button" class="btn btn-sm btn-danger js-slider-remove">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <input type="hidden" name="remove_{{ $key }}" value="0">
                                        <div class="dropify dropify-slider"
                                            data-initial-url="{{ $sliderInitialUrls[$key] ?? '' }}">
                                            <input class="dropify-input js-dropify" type="file"
                                                name="{{ $key }}" accept="image/*"
                                                data-remove-target="remove_{{ $key }}">
                                            <div class="dropify-inner">
                                                <div class="dropify-preview" aria-hidden="true"><img alt="" />
                                                </div>
                                                <div class="dropify-meta">
                                                    <div class="dropify-title">Drop an image here or click to upload</div>
                                                    <div class="dropify-hint">Allowed: JPG/PNG/WEBP up to 2MB</div>
                                                    <div class="dropify-controls">
                                                        <button type="button"
                                                            class="btn btn-sm btn-secondary js-dropify-browse">Choose
                                                            file</button>
                                                        <button type="button"
                                                            class="btn btn-sm btn-danger js-dropify-clear">Remove</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @error($key)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @empty
                                    <div class="empty-state-slider">
                                        <i class="fas fa-images"></i>
                                        <p>No sliders added yet. Click "Add Slider" to create one.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions" style="margin-top: 24px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Slider Information Modal -->
    <div id="sliderInfoModal" class="slider-info-modal" style="display: none;">
        <div class="modal-overlay" id="modalOverlay"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fas fa-info-circle"></i>
                    Slider Upload Guide
                </h3>
                <button type="button" class="modal-close-btn" id="modalCloseBtn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="info-section">
                    <h4><i class="fas fa-check-circle"></i> File Requirements</h4>
                    <ul>
                        <li><strong>Allowed Formats:</strong> JPG, JPEG, PNG, WEBP</li>
                        <li><strong>Maximum File Size:</strong> 2 MB (2048 KB)</li>
                        <li><strong>Recommended Dimensions:</strong> 1200x400 pixels or similar aspect ratio</li>
                        <li><strong>Image Quality:</strong> High resolution for best display</li>
                    </ul>
                </div>

                <div class="info-section">
                    <h4><i class="fas fa-upload"></i> How to Add Sliders</h4>
                    <ol>
                        <li>Click the <strong>"Add Slider"</strong> button to create a new slider field</li>
                        <li>Click on the upload area or drag & drop your image</li>
                        <li>The image will be validated automatically (size and format)</li>
                        <li>Preview will appear once the image is selected</li>
                        <li>Click <strong>"Save Settings"</strong> to upload all sliders</li>
                    </ol>
                </div>

                <div class="info-section">
                    <h4><i class="fas fa-edit"></i> Managing Sliders</h4>
                    <ul>
                        <li><strong>Remove:</strong> Click the red trash icon to delete a slider</li>
                        <li><strong>Replace:</strong> Upload a new image to replace the existing one</li>
                        <li><strong>Clear:</strong> Use the "Remove" button in the upload area to clear selection</li>
                        <li><strong>Reorder:</strong> Sliders are automatically numbered and compacted</li>
                    </ul>
                </div>

                <div class="info-section warning-section">
                    <h4><i class="fas fa-exclamation-triangle"></i> Important Notes</h4>
                    <ul>
                        <li>Images larger than 2MB will be <strong>rejected</strong> - compress them first</li>
                        <li>Use online tools like <a href="https://tinypng.com/" target="_blank">TinyPNG</a> or <a
                                href="https://squoosh.app/" target="_blank">Squoosh</a> to compress images</li>
                        <li>Only image files are accepted (no PDFs, videos, etc.)</li>
                        <li>Changes are not saved until you click the <strong>"Save Settings"</strong> button</li>
                    </ul>
                </div>

                <div class="info-section tip-section">
                    <h4><i class="fas fa-lightbulb"></i> Pro Tips</h4>
                    <ul>
                        <li>Use landscape-oriented images for better display</li>
                        <li>Optimize images before uploading for faster page load</li>
                        <li>Test sliders on different screen sizes</li>
                        <li>Keep text overlays readable with good contrast</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="modalCloseFooterBtn">
                    <i class="fas fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function() {
                // Configuration
                const CONFIG = {
                    maxFileSize: 2 * 1024 * 1024, // 2MB in bytes
                    allowedTypes: ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'],
                    allowedExtensions: ['jpg', 'jpeg', 'png', 'webp']
                };

                // Utility: Show error message
                function showError(wrapper, message) {
                    console.error('[Dropify Error]', message);

                    // Remove any existing error messages
                    const existingError = wrapper.querySelector('.dropify-error-message');
                    if (existingError) existingError.remove();

                    // Create and append error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'dropify-error-message';
                    errorDiv.style.cssText =
                        'color: #dc3545; font-size: 12px; margin-top: 8px; padding: 8px; background: #fee; border-radius: 4px; border-left: 3px solid #dc3545;';
                    errorDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + message;
                    wrapper.appendChild(errorDiv);

                    // Auto-hide after 5 seconds
                    setTimeout(function() {
                        if (errorDiv.parentNode) errorDiv.remove();
                    }, 5000);
                }

                // Utility: Validate file
                function validateFile(file, fieldName) {
                    console.log('[Dropify] Validating file:', {
                        name: file.name,
                        size: file.size,
                        type: file.type,
                        field: fieldName
                    });

                    // Check if file exists
                    if (!file) {
                        return {
                            valid: false,
                            error: 'No file selected'
                        };
                    }

                    // Check file size
                    if (file.size > CONFIG.maxFileSize) {
                        const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
                        return {
                            valid: false,
                            error: `File is too large (${sizeMB}MB). Maximum allowed size is 2MB. Please compress your image first.`
                        };
                    }

                    if (file.size === 0) {
                        return {
                            valid: false,
                            error: 'File is empty (0 bytes)'
                        };
                    }

                    // Check file type
                    const fileExt = file.name.split('.').pop().toLowerCase();
                    if (!CONFIG.allowedExtensions.includes(fileExt)) {
                        return {
                            valid: false,
                            error: `Invalid file type (.${fileExt}). Allowed types: JPG, PNG, WEBP`
                        };
                    }

                    // Check MIME type
                    if (file.type && !CONFIG.allowedTypes.includes(file.type)) {
                        return {
                            valid: false,
                            error: `Invalid file format (${file.type}). Only images are allowed.`
                        };
                    }

                    console.log('[Dropify] File validation passed');
                    return {
                        valid: true
                    };
                }

                // Utility: Handle file selection
                function handleFileSelection(wrapper, fileInput, file, removeHidden, initialUrl) {
                    const fieldName = fileInput.name || 'image';

                    // Clear any previous errors
                    const existingError = wrapper.querySelector('.dropify-error-message');
                    if (existingError) existingError.remove();

                    if (!file) {
                        console.log('[Dropify] No file selected, reverting to initial');
                        setPreview(wrapper, initialUrl);
                        return false;
                    }

                    // Validate file
                    const validation = validateFile(file, fieldName);
                    if (!validation.valid) {
                        showError(wrapper, validation.error);
                        clearInput(fileInput);
                        setPreview(wrapper, initialUrl);
                        return false;
                    }

                    // File is valid, show preview
                    try {
                        const url = URL.createObjectURL(file);
                        setPreview(wrapper, url);
                        if (removeHidden) removeHidden.value = '0';

                        console.log('[Dropify] File ready for upload:', {
                            name: file.name,
                            size: (file.size / 1024).toFixed(2) + ' KB',
                            type: file.type
                        });

                        return true;
                    } catch (e) {
                        console.error('[Dropify] Error creating preview:', e);
                        showError(wrapper, 'Failed to preview image: ' + e.message);
                        clearInput(fileInput);
                        return false;
                    }
                }

                function initDropify(wrapper) {
                    console.log('[Dropify] Initializing dropify widget');

                    const fileInput = wrapper.querySelector('input[type="file"].js-dropify');
                    if (!fileInput) {
                        console.warn('[Dropify] No file input found in wrapper');
                        return;
                    }

                    const initialUrl = wrapper.getAttribute('data-initial-url') || '';
                    setPreview(wrapper, initialUrl);

                    const browseBtn = wrapper.querySelector('.js-dropify-browse');
                    const clearBtn = wrapper.querySelector('.js-dropify-clear');
                    const removeTarget = fileInput.getAttribute('data-remove-target');
                    const removeHidden = removeTarget ? document.querySelector('input[name="' + removeTarget + '"]') : null;

                    if (browseBtn) {
                        browseBtn.addEventListener('click', function(e) {
                            e.preventDefault();
                            console.log('[Dropify] Browse button clicked');
                            fileInput.click();
                        });
                    }

                    // Click anywhere on the dropify (except controls) to browse
                    wrapper.addEventListener('click', function(e) {
                        if (e.target && e.target.closest && e.target.closest('.dropify-controls')) return;
                        if (e.target && (e.target.tagName === 'BUTTON' || e.target.tagName === 'INPUT' || e.target
                                .tagName === 'LABEL')) return;
                        console.log('[Dropify] Wrapper clicked, opening file browser');
                        fileInput.click();
                    });

                    if (clearBtn) {
                        clearBtn.addEventListener('click', function(e) {
                            e.preventDefault();
                            console.log('[Dropify] Clear button clicked');
                            clearInput(fileInput);
                            setPreview(wrapper, '');
                            if (removeHidden) removeHidden.value = '1';

                            // Clear any error messages
                            const existingError = wrapper.querySelector('.dropify-error-message');
                            if (existingError) existingError.remove();
                        });
                    }

                    fileInput.addEventListener('change', function(e) {
                        console.log('[Dropify] File input changed');
                        const file = fileInput.files && fileInput.files[0];
                        handleFileSelection(wrapper, fileInput, file, removeHidden, initialUrl);
                    });

                    // Drag & drop
                    ['dragenter', 'dragover'].forEach(function(evt) {
                        wrapper.addEventListener(evt, function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            wrapper.classList.add('is-dragover');
                        });
                    });

                    ['dragleave', 'drop'].forEach(function(evt) {
                        wrapper.addEventListener(evt, function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            wrapper.classList.remove('is-dragover');
                        });
                    });

                    wrapper.addEventListener('drop', function(e) {
                        console.log('[Dropify] File dropped');
                        const dt = e.dataTransfer;
                        if (!dt || !dt.files || !dt.files.length) {
                            console.warn('[Dropify] No files in drop event');
                            return;
                        }

                        const file = dt.files[0];

                        // Validate that it's an image
                        if (!file.type || !file.type.startsWith('image/')) {
                            showError(wrapper, 'Please drop an image file (JPG, PNG, or WEBP)');
                            return;
                        }

                        try {
                            const transfer = new DataTransfer();
                            transfer.items.add(file);
                            fileInput.files = transfer.files;

                            handleFileSelection(wrapper, fileInput, file, removeHidden, initialUrl);
                        } catch (e) {
                            console.error('[Dropify] Error handling dropped file:', e);
                            showError(wrapper, 'Failed to process dropped file: ' + e.message);
                        }
                    });
                }

                function setPreview(wrapper, url) {
                    const preview = wrapper.querySelector('.dropify-preview');
                    const img = wrapper.querySelector('.dropify-preview img');
                    if (!preview || !img) return;

                    if (url) {
                        img.src = url;
                        img.style.display = 'block';
                        preview.setAttribute('aria-hidden', 'false');
                        preview.style.display = 'block';
                    } else {
                        img.removeAttribute('src');
                        img.style.display = 'none';
                        preview.setAttribute('aria-hidden', 'true');
                        preview.style.display = 'none';
                    }
                }

                function clearInput(fileInput) {
                    try {
                        fileInput.value = '';
                        console.log('[Dropify] File input cleared');
                    } catch (e) {
                        console.error('[Dropify] Error clearing input:', e);
                    }
                }


                // Init all existing dropify widgets
                console.log('[Dropify] Initializing all dropify widgets...');
                document.querySelectorAll('.dropify').forEach(function(wrapper, index) {
                    console.log('[Dropify] Init widget #' + (index + 1));
                    initDropify(wrapper);
                });
                console.log('[Dropify] All widgets initialized');

                function getNextSliderNumber() {
                    const numbers = Array.from(document.querySelectorAll('.js-slider-field'))
                        .map(function(el) {
                            return parseInt(el.getAttribute('data-slider-number') || '0', 10);
                        })
                        .filter(function(n) {
                            return !isNaN(n) && n > 0;
                        });
                    const max = numbers.length ? Math.max.apply(null, numbers) : 0;
                    return max + 1;
                }

                function createSliderField(n) {
                    const key = 'slider' + n;
                    const wrapper = document.createElement('div');
                    wrapper.className = 'slider-item js-slider-field';
                    wrapper.setAttribute('data-slider-number', String(n));

                    wrapper.innerHTML = '' +
                        '<div class="slider-header">' +
                        '  <span class="slider-label"><i class="fas fa-image"></i> Slider ' + n + '</span>' +
                        '  <button type="button" class="btn btn-sm btn-danger js-slider-remove"><i class="fas fa-trash"></i></button>' +
                        '</div>' +
                        '<input type="hidden" name="remove_' + key + '" value="0">' +
                        '<div class="dropify dropify-slider" data-initial-url="">' +
                        '  <input class="dropify-input js-dropify" type="file" name="' + key +
                        '" accept="image/*" data-remove-target="remove_' + key + '">' +
                        '  <div class="dropify-inner">' +
                        '    <div class="dropify-preview" aria-hidden="true"><img alt="" /></div>' +
                        '    <div class="dropify-meta">' +
                        '      <div class="dropify-title">Drop an image here or click to upload</div>' +
                        '      <div class="dropify-hint">Allowed: JPG/PNG/WEBP up to 2MB</div>' +
                        '      <div class="dropify-controls">' +
                        '        <button type="button" class="btn btn-sm btn-secondary js-dropify-browse">Choose file</button>' +
                        '        <button type="button" class="btn btn-sm btn-danger js-dropify-clear">Remove</button>' +
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
                    addBtn.addEventListener('click', function() {
                        // Remove empty state if it exists
                        const emptyState = slidersGrid.querySelector('.empty-state-slider');
                        if (emptyState) {
                            emptyState.remove();
                        }

                        const n = getNextSliderNumber();
                        const field = createSliderField(n);
                        slidersGrid.appendChild(field);
                    });
                }

                // Remove slider field behavior
                document.addEventListener('click', function(e) {
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
                    const hasInitial = !!(field.querySelector('.dropify') && field.querySelector('.dropify')
                        .getAttribute('data-initial-url'));
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

                    picker.addEventListener('input', function() {
                        hex.value = picker.value;
                    });

                    hex.addEventListener('input', function() {
                        const n = normalize(hex.value);
                        if (n) picker.value = n;
                    });

                    // initial normalize
                    const initial = normalize(hex.value) || normalize(picker.value) || '#000000';
                    picker.value = initial;
                    hex.value = initial;
                })();

                // Form submission validation
                const form = document.querySelector('form[action*="logo.store"]');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        console.log('[Form] Submission started');

                        let hasErrors = false;
                        const errors = [];

                        // Check all file inputs for oversized files
                        document.querySelectorAll('input[type="file"]').forEach(function(input) {
                            if (input.files && input.files[0]) {
                                const file = input.files[0];
                                const fieldName = input.name || 'unknown field';

                                // Validate file size client-side
                                if (file.size > CONFIG.maxFileSize) {
                                    const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
                                    const maxMB = (CONFIG.maxFileSize / (1024 * 1024)).toFixed(0);
                                    errors.push(
                                        `${fieldName}: File too large (${sizeMB}MB). Max size is ${maxMB}MB`
                                    );
                                    hasErrors = true;
                                    console.error('[Form] File size error:', fieldName, sizeMB + 'MB');
                                }

                                // Validate file type
                                const ext = file.name.split('.').pop().toLowerCase();
                                if (!CONFIG.allowedExtensions.includes(ext)) {
                                    errors.push(
                                        `${fieldName}: Invalid file type (.${ext}). Allowed: JPG, PNG, WEBP`
                                    );
                                    hasErrors = true;
                                    console.error('[Form] File type error:', fieldName, ext);
                                }

                                console.log('[Form] Validated file:', {
                                    field: fieldName,
                                    name: file.name,
                                    size: (file.size / 1024).toFixed(2) + ' KB',
                                    type: file.type
                                });
                            }
                        });

                        if (hasErrors) {
                            e.preventDefault();

                            // Show consolidated error message
                            let errorHtml =
                                '<div class="alert alert-error" style="margin: 16px 0;"><i class="fas fa-exclamation-circle"></i><div><strong>Please fix the following errors before submitting:</strong><ul style="margin: 8px 0 0 20px; padding: 0;">';
                            errors.forEach(function(error) {
                                errorHtml += '<li>' + error + '</li>';
                            });
                            errorHtml += '</ul></div></div>';

                            // Remove existing client-side errors
                            const existingClientError = document.querySelector('.client-side-error');
                            if (existingClientError) existingClientError.remove();

                            // Insert error before form
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'client-side-error';
                            errorDiv.innerHTML = errorHtml;
                            form.parentNode.insertBefore(errorDiv, form);

                            // Scroll to error
                            errorDiv.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });

                            console.error('[Form] Submission blocked due to validation errors');
                            return false;
                        }

                        console.log('[Form] Validation passed, submitting...');

                        // Show loading state
                        const submitBtn = form.querySelector('button[type="submit"]');
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                        }
                    });
                }

                // Modal functionality for Slider Information
                (function initSliderInfoModal() {
                    const modal = document.getElementById('sliderInfoModal');
                    const infoBtn = document.getElementById('sliderInfoBtn');
                    const closeBtn = document.getElementById('modalCloseBtn');
                    const closeFooterBtn = document.getElementById('modalCloseFooterBtn');
                    const overlay = document.getElementById('modalOverlay');

                    if (!modal || !infoBtn) {
                        console.warn('[Modal] Modal elements not found');
                        return;
                    }

                    // Open modal
                    function openModal() {
                        console.log('[Modal] Opening slider info modal');
                        modal.style.display = 'flex';
                        document.body.style.overflow = 'hidden'; // Prevent background scrolling

                        // Add fade-in animation
                        setTimeout(function() {
                            modal.classList.add('modal-active');
                        }, 10);
                    }

                    // Close modal
                    function closeModal() {
                        console.log('[Modal] Closing slider info modal');
                        modal.classList.remove('modal-active');

                        setTimeout(function() {
                            modal.style.display = 'none';
                            document.body.style.overflow = ''; // Restore scrolling
                        }, 300); // Wait for fade-out animation
                    }

                    // Event listeners
                    infoBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        openModal();
                    });

                    if (closeBtn) {
                        closeBtn.addEventListener('click', function(e) {
                            e.preventDefault();
                            closeModal();
                        });
                    }

                    if (closeFooterBtn) {
                        closeFooterBtn.addEventListener('click', function(e) {
                            e.preventDefault();
                            closeModal();
                        });
                    }

                    // Close when clicking overlay (outside modal content)
                    if (overlay) {
                        overlay.addEventListener('click', function(e) {
                            if (e.target === overlay) {
                                closeModal();
                            }
                        });
                    }

                    // Close with Escape key
                    document.addEventListener('keydown', function(e) {
                        if (e.key === 'Escape' && modal.style.display === 'flex') {
                            closeModal();
                        }
                    });

                    console.log('[Modal] Slider info modal initialized');
                })();

                console.log('[App] All initialization complete');
            })();
        </script>
    @endpush
@endsection
