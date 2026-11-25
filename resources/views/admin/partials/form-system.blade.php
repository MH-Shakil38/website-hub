{{--
    Shared admin form system.
    Every admin / SaaS / module-admin page extends admin.layouts.master, which
    includes this partial, so form controls share one look: the same height,
    padding, font size, radius, colours and focus ring as the login form
    (.form-control-sb in auth/layouts/app.blade.php). Change sizes here, not per page.
--}}
<style>
    :root {
        /* Mirrors the login form input (.form-control-sb) */
        --sb-control-h: clamp(42px, 5.5vh, 52px);
        --sb-control-h-compact: 36px;
        --sb-control-px: 14px;
        --sb-control-fs: 14px;
        --sb-control-radius: 10px;
        --sb-control-bg: #F8FAFC;
        --sb-control-border: #E2E8F0;
        --sb-control-border-hover: #CBD5E1;
        --sb-control-text: #0F172A;
        --sb-control-placeholder: #94A3B8;
        --sb-control-focus: #EC134A;
        --sb-control-ring: 0 0 0 3px rgba(236, 19, 74, 0.10);
        --sb-control-danger: #E11D48;
        --sb-control-success: #16A34A;
        --sb-label-fs: 13.5px;
        --sb-label-color: #0F172A;
    }

    /* ---------- Labels & helper text ---------- */
    .sb-main .form-label {
        font-size: var(--sb-label-fs) !important;
        font-weight: 600 !important;
        line-height: 1.35;
        color: var(--sb-label-color);
        margin-bottom: 7px;
    }

    .sb-main .form-label.text-primary { color: var(--sb-control-focus) !important; }

    .sb-main .form-text {
        font-size: 12.5px;
        color: #64748B;
        margin-top: 6px;
    }

    /* ---------- Text inputs & selects (one size everywhere) ---------- */
    .sb-main .form-control,
    .sb-main .form-select {
        height: var(--sb-control-h) !important;
        min-height: var(--sb-control-h);
        padding-top: 0;
        padding-bottom: 0;
        padding-left: var(--sb-control-px);
        font-size: var(--sb-control-fs);
        font-family: inherit;
        line-height: 1.4;
        color: var(--sb-control-text);
        background-color: var(--sb-control-bg);
        border: 1px solid var(--sb-control-border);
        border-radius: var(--sb-control-radius);
        box-shadow: none;
        transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease;
    }

    .sb-main .form-control { padding-right: var(--sb-control-px); }
    .sb-main .form-select { padding-right: 38px; background-position: right 14px center; }

    .sb-main .form-control::placeholder { color: var(--sb-control-placeholder); opacity: 1; }

    .sb-main .form-control:hover:not(:disabled):not([readonly]):not(:focus),
    .sb-main .form-select:hover:not(:disabled):not(:focus) {
        border-color: var(--sb-control-border-hover);
    }

    .sb-main .form-control:focus,
    .sb-main .form-select:focus {
        background-color: #FFFFFF;
        border-color: var(--sb-control-focus);
        box-shadow: var(--sb-control-ring);
        outline: 0;
    }

    .sb-main .form-control:disabled,
    .sb-main .form-control[readonly],
    .sb-main .form-select:disabled {
        background-color: #F1F5F9;
        color: #64748B;
        cursor: not-allowed;
    }

    .sb-main .form-control[readonly] { cursor: default; }

    /* Bootstrap's -sm variants become the same size as everything else ... */
    .sb-main .form-control-sm,
    .sb-main .form-select-sm {
        font-size: var(--sb-control-fs);
        border-radius: var(--sb-control-radius);
    }

    /* ... except controls sitting inline inside a data-table row, which stay compact. */
    .sb-main table .form-control,
    .sb-main table .form-select {
        height: var(--sb-control-h-compact) !important;
        min-height: var(--sb-control-h-compact);
        font-size: 13px;
        border-radius: 8px;
    }

    /* Textareas grow with content, keep login padding/look */
    .sb-main textarea.form-control {
        height: auto !important;
        min-height: 96px;
        padding: 11px var(--sb-control-px);
        line-height: 1.55;
        resize: vertical;
    }

    .sb-main textarea.form-control[rows="1"],
    .sb-main textarea.form-control[rows="2"] { min-height: 72px; }

    /* File inputs: vertically centred "Choose file" button */
    .sb-main .form-control[type="file"] {
        padding-left: 0;
        line-height: calc(var(--sb-control-h) - 2px);
        overflow: hidden;
        cursor: pointer;
    }

    .sb-main .form-control[type="file"]::file-selector-button {
        height: 100%;
        margin: 0 14px 0 0;
        padding: 0 16px;
        border: 0;
        border-right: 1px solid var(--sb-control-border);
        border-radius: 0;
        background: #EEF2F6;
        color: #334155;
        font-weight: 600;
        font-size: 13px;
    }

    .sb-main .form-control[type="file"]:hover:not(:disabled)::file-selector-button { background: #E2E8F0; }

    /* Colour pickers */
    .sb-main .form-control-color {
        padding: 5px;
        min-width: 56px;
        cursor: pointer;
    }

    .sb-main .form-control-color::-webkit-color-swatch { border: 0; border-radius: 6px; }
    .sb-main .form-control-color::-moz-color-swatch { border: 0; border-radius: 6px; }

    /* ---------- Search fields with a leading icon ---------- */
    /* The icon is an absolutely positioned <i class="bi bi-search"> placed before the input. */
    .sb-main .form-control.sb-search-input { padding-left: 40px; }

    /* ---------- Input groups ---------- */
    .sb-main .input-group > .input-group-text {
        height: var(--sb-control-h);
        padding: 0 var(--sb-control-px);
        font-size: 13.5px;
        color: #64748B;
        background-color: #EEF2F6;
        border: 1px solid var(--sb-control-border);
        border-radius: var(--sb-control-radius);
    }

    .sb-main .input-group > .btn { height: var(--sb-control-h); min-height: 0; }

    .sb-main .input-group:focus-within > .input-group-text { border-color: var(--sb-control-focus); }

    /* ---------- Checkboxes, radios & switches ---------- */
    .sb-main .form-check-input {
        width: 18px;
        height: 18px;
        margin-top: .15em;
        border: 1.5px solid #CBD5E1;
        background-color: #FFFFFF;
        cursor: pointer;
    }

    .sb-main .form-check-input[type="checkbox"]:not([role="switch"]) { border-radius: 5px; }

    .sb-main .form-switch .form-check-input {
        width: 2.3em;
        height: 1.25em;
        margin-top: .1em;
    }

    .sb-main .form-check-input:checked {
        background-color: var(--sb-control-focus);
        border-color: var(--sb-control-focus);
    }

    .sb-main .form-check-input:focus {
        border-color: var(--sb-control-focus);
        box-shadow: var(--sb-control-ring);
    }

    .sb-main .form-switch .form-check-input:focus:not(:checked) {
        --bs-form-switch-bg: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23CBD5E1'/%3e%3c/svg%3e");
    }

    .sb-main .form-check-label { color: #334155; cursor: pointer; }

    /* ---------- Validation ---------- */
    .sb-main .form-control.is-invalid,
    .sb-main .form-select.is-invalid,
    .sb-main .was-validated .form-control:invalid {
        border-color: var(--sb-control-danger);
        background-color: #FFF8F9;
    }

    .sb-main .form-control.is-invalid:focus,
    .sb-main .form-select.is-invalid:focus {
        border-color: var(--sb-control-danger);
        box-shadow: 0 0 0 3px rgba(225, 29, 72, 0.12);
    }

    .sb-main .form-control.is-valid,
    .sb-main .form-select.is-valid { border-color: var(--sb-control-success); }

    .sb-main .invalid-feedback,
    .sb-main .text-danger.small {
        font-size: 12.5px;
        font-weight: 500;
        color: var(--sb-control-danger) !important;
        margin-top: 6px;
    }

    /* ---------- Buttons: same height & radius as the inputs ---------- */
    .sb-main .btn {
        --bs-btn-font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: var(--sb-control-h);
        padding: 0 20px;
        font-size: 14px;
        line-height: 1.2;
        border-radius: var(--sb-control-radius);
        transition: background-color .15s ease, border-color .15s ease, color .15s ease, box-shadow .15s ease;
    }

    .sb-main .btn.w-100 { display: flex; }

    .sb-main .btn:focus-visible { box-shadow: var(--sb-control-ring); }

    .sb-main .btn-sm {
        min-height: 34px;
        padding: 0 12px;
        font-size: 12.5px;
        gap: 6px;
        border-radius: 8px;
    }

    .sb-main .btn-link { min-height: 0; padding: 0; }
    .sb-main .btn-close { min-height: 0; padding: .25em; }

    .sb-main table .btn:not(.btn-sm) { min-height: var(--sb-control-h-compact); padding: 0 14px; }

    /* Buttons in a form row line up with the inputs next to them */
    .sb-main form .row > [class*="col"] > .btn:not(.btn-sm),
    .sb-main form .d-flex > .btn:not(.btn-sm) { height: var(--sb-control-h); }

    /* ---------- Spacing inside forms & modals ---------- */
    .sb-main form .mb-3 { margin-bottom: 18px !important; }

    .sb-main .modal-header { padding: 18px 24px; border-bottom: 1px solid #EEF2F6; }
    .sb-main .modal-body { padding: 22px 24px; }
    .sb-main .modal-footer { padding: 14px 24px; border-top: 1px solid #EEF2F6; gap: 8px; }
    .sb-main .modal-footer > * { margin: 0; }
    .sb-main .modal-title { font-size: 17px; font-weight: 700; color: #0F172A; }
</style>
