@php
    $dzId = 'dz_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $name) . '_' . uniqid();
    $height = $height ?? '165px';
    $fit = $fit ?? 'cover';
    $hint = $hint ?? 'Drag & drop image here, or click to change';
@endphp

<div class="sb-dropzone-wrap">
    @if(!empty($label))
        <label class="form-label fw-bold small text-dark mb-2 d-flex align-items-center justify-content-between">
            <span class="sb-dz-label-text">{{ $label }}</span>
            <span class="badge rounded-pill fw-semibold" id="{{ $dzId }}_badge" style="background: #F1F5F9; color: #64748B; font-size: 10.5px;">Current / Default</span>
        </label>
    @endif

    <div class="sb-dropzone"
         id="{{ $dzId }}"
         data-original-src="{{ $preview }}"
         style="position: relative; height: {{ $height }}; border: 2px dashed #CBD5E1; border-radius: 14px; background: #F8FAFC; overflow: hidden; cursor: pointer; transition: all 0.2s ease; display: flex; align-items: center; justify-content: center;">

        <!-- Hidden File Input (Inside the same box) -->
        <input type="file"
               name="{{ $name }}"
               id="{{ $dzId }}_input"
               accept="image/*"
               style="position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 5;">

        <!-- Live Image Preview -->
        <img src="{{ $preview }}"
             id="{{ $dzId }}_img"
             alt="{{ $label ?? 'Preview' }}"
             style="width: 100%; height: 100%; object-fit: {{ $fit }}; padding: {{ $fit === 'contain' ? '18px' : '0' }}; transition: transform 0.25s ease;">

        <!-- Hover / Drag & Drop Overlay -->
        <div class="sb-dz-overlay"
             style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(15, 23, 42, 0.82) 0%, rgba(15, 23, 42, 0.35) 55%, rgba(15, 23, 42, 0.15) 100%); display: flex; flex-direction: column; align-items: center; justify-content: flex-end; padding: 14px; text-align: center; color: #FFFFFF; pointer-events: none; transition: opacity 0.2s ease;">
            <div style="width: 34px; height: 34px; border-radius: 50%; background: rgba(236, 19, 74, 0.92); color: #fff; display: flex; align-items: center; justify-content: center; margin-bottom: 5px; box-shadow: 0 4px 10px rgba(0,0,0,0.25);">
                <i class="bi bi-cloud-arrow-up-fill" style="font-size: 15px;"></i>
            </div>
            <div style="font-size: 12px; font-weight: 700; letter-spacing: 0.01em;">{{ $hint }}</div>
            <div style="font-size: 10.5px; color: #E2E8F0; opacity: 0.9;" id="{{ $dzId }}_filename">PNG, JPG, WEBP or SVG (Untouched keeps default)</div>
        </div>

        <!-- Undo Selection Button (Only visible after user picks a new file) -->
        <button type="button"
                id="{{ $dzId }}_reset"
                title="Cancel new upload"
                style="display: none; position: absolute; top: 10px; right: 10px; z-index: 10; background: rgba(15, 23, 42, 0.85); color: #fff; border: none; border-radius: 8px; padding: 4px 10px; font-size: 11px; font-weight: 600; cursor: pointer;">
            <i class="bi bi-x-lg me-1"></i>Undo
        </button>
    </div>
</div>

@once
    @push('module_styles')
    <style>
        .sb-dropzone:hover {
            border-color: #EC134A !important;
            box-shadow: 0 6px 18px rgba(236, 19, 74, 0.10);
        }
        .sb-dropzone:hover img {
            transform: scale(1.03);
        }
        .sb-dropzone.dragover {
            border-color: #EC134A !important;
            background: #FFF1F5 !important;
            transform: scale(1.01);
        }
    </style>
    @endpush

    @push('module_scripts')
    <script>
        window.initSbDropzones = function () {
            document.querySelectorAll('.sb-dropzone:not([data-dz-ready="1"])').forEach(function (zone) {
                zone.setAttribute('data-dz-ready', '1');
                const input = zone.querySelector('input[type="file"]');
                const img = zone.querySelector('img');
                const filenameEl = zone.querySelector('[id$="_filename"]');
                const resetBtn = zone.querySelector('[id$="_reset"]');
                const badgeEl = document.getElementById(zone.id + '_badge');
                const originalSrc = zone.getAttribute('data-original-src');

                function previewFile(file) {
                    if (!file || !file.type.startsWith('image/')) return;
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        img.src = e.target.result;
                        if (filenameEl) filenameEl.textContent = 'Selected: ' + file.name;
                        if (resetBtn) resetBtn.style.display = 'inline-block';
                        if (badgeEl) {
                            badgeEl.textContent = 'New Photo Ready';
                            badgeEl.style.background = '#DCFCE7';
                            badgeEl.style.color = '#15803D';
                        }
                    };
                    reader.readAsDataURL(file);
                }

                input.addEventListener('change', function () {
                    if (this.files && this.files[0]) {
                        previewFile(this.files[0]);
                    }
                });

                ['dragenter', 'dragover'].forEach(function (evt) {
                    zone.addEventListener(evt, function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        zone.classList.add('dragover');
                    });
                });

                ['dragleave', 'drop'].forEach(function (evt) {
                    zone.addEventListener(evt, function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        zone.classList.remove('dragover');
                    });
                });

                zone.addEventListener('drop', function (e) {
                    const files = e.dataTransfer.files;
                    if (files && files.length > 0) {
                        input.files = files;
                        previewFile(files[0]);
                    }
                });

                if (resetBtn) {
                    resetBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        input.value = '';
                        img.src = originalSrc;
                        if (filenameEl) filenameEl.textContent = 'PNG, JPG, WEBP or SVG (Untouched keeps default)';
                        resetBtn.style.display = 'none';
                        if (badgeEl) {
                            badgeEl.textContent = 'Current / Default';
                            badgeEl.style.background = '#F1F5F9';
                            badgeEl.style.color = '#64748B';
                        }
                    });
                }
            });
        };

        document.addEventListener('DOMContentLoaded', function () {
            window.initSbDropzones();
        });
    </script>
    @endpush
@endonce
