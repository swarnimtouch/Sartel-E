@extends('layouts.app')

@section('title', 'Sartel-E || Add Doctor')
@section('page_title', 'Add Doctor')
@section('page_subtitle', 'Fill in the details to register a new doctor.')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <style>
        .form-card { max-width: 700px; }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group.full { grid-column: 1 / -1; }

        .form-group label {
            font-size: .82rem;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        /* ── Shared input style ── */
        .form-group input {
            padding: 11px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 15px;
            font-family: inherit;
            color: #0f172a;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
            background: #f8fafc;
            width: 100%;
            box-sizing: border-box;
        }

        .form-group input:focus {
            border-color: #38bdf8;
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(56,189,248,.12);
        }

        .form-group input[readonly] {
            background: #f1f5f9;
            color: #94a3b8;
            cursor: not-allowed;
        }

        .form-group input.error {
            border-color: #f43f5e;
            background-color: #fff1f2;
        }

        /* ════════════════════════════════════
           SELECT2 — only for #doctor_id
           ════════════════════════════════════ */
        .select2-container { width: 100% !important; }

        .select2-container--default .select2-selection--single {
            height: 46px;
            padding: 0 40px 0 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            background: #f8fafc;
            display: flex;
            align-items: center;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #0f172a;
            font-size: 15px;
            font-family: inherit;
            line-height: 1;
            padding: 0;
            position: static;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #94a3b8;
            font-size: 15px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            height: auto;
            width: auto;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #94a3b8 transparent transparent;
            border-width: 5px 4px 0;
            transition: border-color .2s;
        }

        .select2-container--default.select2-container--open
        .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent #38bdf8;
            border-width: 0 4px 5px;
        }

        .select2-container--default .select2-selection--single .select2-selection__clear {
            margin-right: 20px;
            color: #94a3b8;
            font-size: 16px;
            font-weight: 400;
            line-height: 1;
        }

        .select2-container--default .select2-selection--single .select2-selection__clear:hover {
            color: #f43f5e;
        }

        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #38bdf8;
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(56,189,248,.12);
        }

        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #38bdf8;
            background-color: #fff;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        /* Error state for Select2 */
        .select2-error.select2-container--default .select2-selection--single {
            border-color: #f43f5e !important;
            background-color: #fff1f2 !important;
            box-shadow: none !important;
        }

        .select2-container--disabled .select2-selection--single {
            cursor: not-allowed !important;
            opacity: .55;
        }

        /* Dropdown panel */
        .select2-dropdown {
            border: 1.5px solid #38bdf8;
            border-top: none;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 8px 24px rgba(0,0,0,.1);
            background: #fff;
            overflow: hidden;
        }

        .select2-container--default .select2-search--dropdown {
            padding: 10px 12px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            width: 100%;
            padding: 8px 12px 8px 34px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            color: #0f172a;
            background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'/%3E%3C/svg%3E") no-repeat 10px center;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56,189,248,.1);
        }

        .select2-results__options {
            max-height: 220px;
            overflow-y: auto;
            padding: 4px 0;
        }

        .select2-container--default .select2-results__option {
            padding: 10px 14px;
            font-size: 14px;
            font-family: inherit;
            color: #334155;
            transition: background .12s;
        }

        .select2-container--default
        .select2-results__option--highlighted.select2-results__option--selectable {
            background: #f0f9ff;
            color: #0284c7;
        }

        .select2-container--default .select2-results__option--selected {
            background: #e0f2fe;
            color: #0369a1;
            font-weight: 600;
        }

        .select2-container--default .select2-results__option.select2-results__message {
            color: #94a3b8;
            font-style: italic;
            font-size: 13px;
            text-align: center;
            padding: 16px;
        }

        /* ════════════════════════════════════
           NATIVE Custom Select — for #language
           ════════════════════════════════════ */
        .custom-select-wrap {
            position: relative;
            width: 100%;
        }

        .custom-select-wrap select {
            width: 100%;
            box-sizing: border-box;
            padding: 11px 44px 11px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 15px;
            font-family: inherit;
            color: #0f172a;
            background: #f8fafc;
            appearance: none;
            -webkit-appearance: none;
            outline: none;
            cursor: pointer;
            transition: border-color .2s, box-shadow .2s;
        }

        .custom-select-wrap select:focus {
            border-color: #38bdf8;
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(56,189,248,.12);
        }

        .custom-select-wrap select.error {
            border-color: #f43f5e;
            background-color: #fff1f2;
        }

        .custom-select-wrap select:disabled {
            opacity: .55;
            cursor: not-allowed;
        }

        /* Custom chevron arrow */
        .custom-select-wrap::after {
            content: '';
            pointer-events: none;
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 6px solid #94a3b8;
            transition: border-top-color .2s;
        }

        .custom-select-wrap:focus-within::after {
            border-top-color: #38bdf8;
        }

        /* ── Error messages ── */
        .err-msg {
            font-size: .78rem;
            color: #f43f5e;
            font-weight: 500;
            display: none;
        }

        .select-empty-msg {
            font-size: .82rem;
            color: #94a3b8;
            margin-top: 4px;
            display: none;
            font-style: italic;
        }

        /* ── Photo area ── */
        .photo-area {
            border: 2px dashed #cbd5e1;
            border-radius: 14px;
            padding: 24px;
            text-align: center;
            background: #f8fafc;
            transition: border .2s;
        }

        .photo-area:hover { border-color: #38bdf8; }

        .upload-label {
            display: inline-block;
            padding: 10px 22px;
            background: linear-gradient(90deg, #38bdf8, #818cf8);
            color: white;
            border-radius: 10px;
            font-size: .85rem;
            font-weight: 600;
            cursor: pointer;
        }

        .photo-area p {
            color: #94a3b8;
            font-size: .82rem;
            margin-top: 8px;
        }

        /* ── Croppie ── */
        #crop-container {
            display: none;
            margin-top: 20px;
            text-align: center;
        }

        #crop { width: 300px; height: 300px; margin: 0 auto; }

        #crop-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 28px;
            background: #0f172a;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: .88rem;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            touch-action: manipulation;
        }

        /* ── Preview ── */
        #preview-wrap {
            display: none;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            margin-top: 16px;
        }

        #preview-wrap img {
            width: 90px; height: 90px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #38bdf8;
        }

        #change-photo {
            background: none;
            border: none;
            color: #38bdf8;
            font-size: .82rem;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            touch-action: manipulation;
        }

        /* ── Actions ── */
        .form-actions {
            margin-top: 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .submit-btn {
            padding: 13px 32px;
            background: linear-gradient(90deg, #38bdf8, #818cf8);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: .95rem;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            transition: opacity .2s;
            touch-action: manipulation;
        }

        .submit-btn:hover    { opacity: .9; }
        .submit-btn:disabled { opacity: .5; cursor: not-allowed; }

        .cancel-link {
            color: #64748b;
            font-size: .88rem;
            text-decoration: none;
            font-weight: 500;
        }

        /* ── MOBILE ── */
        @media (max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; }
            .form-group.full { grid-column: 1; }
            #crop { width: 260px; height: 260px; }
            .photo-area { padding: 18px 14px; }
            .submit-btn { width: 100%; text-align: center; }
            .form-actions { flex-direction: column; gap: 10px; }
            .cancel-link { width: 100%; text-align: center; }
        }
    </style>
@endpush

@section('content')

    <div class="card form-card">

        <form method="POST" action="{{ route('doctors.store') }}" id="doctorForm">
            @csrf
            <input type="hidden" id="employee_id" value="{{ auth()->user()->id }}">
            <input type="hidden" name="cropped_image" id="cropped_image">

            <div class="form-grid">

                {{-- Select Doctor — Select2 ──────────────── --}}
                <div class="form-group full">
                    <label>Select Doctor *</label>
                    {{-- No wrapper div needed — Select2 handles its own container --}}
                    <select id="doctor_id" name="doctor_id">
                        <option value="">Loading doctors...</option>
                    </select>
                    <span class="select-empty-msg" id="no_doctors_msg">✅ All doctors have already been assigned.</span>
                    <span class="err-msg" id="err_doctor">Please select a doctor.</span>
                </div>

                <div class="form-group">
                    <label>MSL Number</label>
                    <input type="text" id="msl_number" name="msl_number" readonly placeholder="Auto-filled on selection">
                </div>

                <div class="form-group">
                    <label>Doctor Birth Date *</label>
                    <input type="date" name="birth_date" id="birth_date">
                    <span class="err-msg" id="err_birth">Birth date is required.</span>
                </div>

                <div class="form-group">
                    <label>Speciality *<br>(as per the doctor's visiting card)</label>
                    <input type="text" name="speciality" id="speciality"
                           placeholder="e.g. Cardiologist"
                           value="{{ old('speciality') }}">
                    <span class="err-msg" id="err_speciality">Speciality is required.</span>
                </div>

                {{-- Language — native custom select ─────── --}}
                <div class="form-group">
                    <label>Select Language *</label>
                    <div class="custom-select-wrap">
                        <select name="language" id="language">
                            <option value="">-- Select Language --</option>
                            <option value="English"     {{ old('language') == 'English'     ? 'selected' : '' }}>English</option>
                            <option value="Hindi"     {{ old('language') == 'Hindi'     ? 'selected' : '' }}>Hindi</option>
                            <option value="Bengali"   {{ old('language') == 'Bengali'   ? 'selected' : '' }}>Bengali</option>
                            <option value="Gujarati"  {{ old('language') == 'Gujarati'  ? 'selected' : '' }}>Gujarati</option>
                            <option value="Marathi"   {{ old('language') == 'Marathi'   ? 'selected' : '' }}>Marathi</option>
                            <option value="Telugu"    {{ old('language') == 'Telugu'    ? 'selected' : '' }}>Telugu</option>
                            <option value="Tamil"     {{ old('language') == 'Tamil'     ? 'selected' : '' }}>Tamil</option>
                            <option value="Odia"      {{ old('language') == 'Odia'      ? 'selected' : '' }}>Odia</option>
                            <option value="Punjabi"   {{ old('language') == 'Punjabi'   ? 'selected' : '' }}>Punjabi</option>
                            <option value="Assamese"  {{ old('language') == 'Assamese'  ? 'selected' : '' }}>Assamese</option>
                            <option value="Kannada"   {{ old('language') == 'Kannada'   ? 'selected' : '' }}>Kannada</option>
                            <option value="Malayalam" {{ old('language') == 'Malayalam' ? 'selected' : '' }}>Malayalam</option>
                        </select>
                    </div>
                    <span class="err-msg" id="err_language">Language is required.</span>
                </div>

                <div class="form-group">
                    <label>Hospital Name *<br>(as per the doctor's visiting card)</label>
                    <input type="text" name="hospital_name" id="hospital_name"
                           placeholder="e.g. City Hospital"
                           value="{{ old('hospital_name') }}">
                    <span class="err-msg" id="err_hospital">Hospital name is required.</span>
                </div>

                <div class="form-group full">
                    <label>Gender *</label>
                    <div style="display:flex;gap:24px;flex-wrap:wrap;padding-top:10px;">
                        @foreach(['Male', 'Female'] as $gender)
                            <label style="display:flex;align-items:center;gap:7px;text-transform:none;letter-spacing:0;cursor:pointer;">
                                <input type="radio" name="gender" value="{{ $gender }}"
                                       style="width:auto;" {{ old('gender') === $gender ? 'checked' : '' }}>
                                {{ $gender }}
                            </label>
                        @endforeach
                    </div>
                    <span class="err-msg" id="err_gender">Please select a gender.</span>
                    @error('gender')<span class="err-msg" style="display:block;">{{ $message }}</span>@enderror
                </div>

                {{-- Photo --}}
                <div class="form-group full">
                    <label>Doctor Photo *</label>

                    <div class="photo-area" id="photoArea">
                        <label for="upload" class="upload-label">📷 Choose Photo</label>
                        <input type="file" id="upload" accept="image/*" style="display:none">
                        <p>JPG, PNG supported • Photo will be cropped to circle</p>
                    </div>
                    <span class="err-msg" id="err_photo">Please upload and crop a photo.</span>

                    <div id="crop-container">
                        <div id="crop"></div>
                        <button type="button" id="crop-btn">✂️ Crop &amp; Use Photo</button>
                    </div>

                    <div id="preview-wrap">
                        <img id="preview-img" src="" alt="Preview">
                        <button type="button" id="change-photo">🔄 Change Photo</button>
                    </div>
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="submit-btn" id="submitBtn">Save Doctor</button>
                <a href="{{ route('doctors.index') }}" class="cancel-link">Cancel</a>
            </div>

        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.js"></script>
    <script>
        var crop         = null;
        var photoCropped = false;

        $(document).ready(function () {

            // ── Init Select2 on Doctor only ──
            $('#doctor_id').select2({
                placeholder: 'Search doctor by name...',
                allowClear:  true,
                width:       '100%',
                language: {
                    noResults: function () { return 'No doctor found'; },
                    searching: function () { return 'Searching...'; }
                }
            });

            // ── Load unassigned doctors via AJAX ──
            $.ajax({
                url:  "{{ route('api.doctors_by_employee') }}",
                type: "GET",
                success: function (res) {
                    var $select = $('#doctor_id');

                    // Destroy → repopulate → reinit cleanly
                    $select.select2('destroy');
                    $select.empty().append('<option value=""></option>');

                    if (res.length === 0) {
                        $('#no_doctors_msg').show();
                        $('#submitBtn').prop('disabled', true);
                    } else {
                        $('#no_doctors_msg').hide();
                        res.forEach(function (doctor) {
                            $select.append(
                                $('<option>', {
                                    value:      doctor.id,
                                    text:       doctor.doctor_name,
                                    'data-msl': doctor.msl_number ?? ''
                                })
                            );
                        });
                    }

                    $select.select2({
                        placeholder: 'Search doctor by name...',
                        allowClear:  true,
                        width:       '100%',
                        disabled:    res.length === 0,
                        language: {
                            noResults: function () { return 'No doctor found'; },
                            searching: function () { return 'Searching...'; }
                        }
                    });
                },
                error: function () {
                    var $select = $('#doctor_id');
                    $select.select2('destroy');
                    $select.empty().append('<option value="">Failed to load doctors</option>');
                    $select.select2({ placeholder: 'Failed to load', width: '100%', disabled: true });
                }
            });

        });

        // ── Auto-fill MSL on doctor change ──
        $(document).on('change', '#doctor_id', function () {
            var doctorId = $(this).val();

            if (doctorId) {
                $(this).next('.select2-container').removeClass('select2-error');
                $('#err_doctor').hide();
            }

            if (!doctorId) { $('#msl_number').val(''); return; }

            var msl = $(this).find(':selected').data('msl');
            if (msl) {
                $('#msl_number').val(msl);
            } else {
                $.ajax({
                    url:  "{{ route('api.msl_number') }}",
                    type: "GET",
                    data: { doctor_id: doctorId },
                    success: function (res) { $('#msl_number').val(res.msl_code); }
                });
            }
        });

        // ── Live clear errors ──
        $('#language').on('change', function () {
            if ($(this).val()) { $(this).removeClass('error'); $('#err_language').hide(); }
        });

        $('#birth_date').on('change', function () {
            if ($(this).val()) { $(this).removeClass('error'); $('#err_birth').hide(); }
        });

        $('#speciality').on('input', function () {
            if ($(this).val().trim()) { $(this).removeClass('error'); $('#err_speciality').hide(); }
        });

        $('#hospital_name').on('input', function () {
            if ($(this).val().trim()) { $(this).removeClass('error'); $('#err_hospital').hide(); }
        });

        // ── Croppie ──
        $('#upload').on('change', function () {
            var file = this.files[0];
            if (!file) return;

            var reader = new FileReader();
            reader.onload = function (e) {
                $('#photoArea').hide();
                $('#preview-wrap').hide().css('display', 'none');
                $('#crop-container').show();

                if (crop) { crop.croppie('destroy'); crop = null; }

                var isMobile = window.innerWidth <= 600;
                crop = $('#crop').croppie({
                    viewport: { width: isMobile ? 180 : 200, height: isMobile ? 180 : 200, type: 'circle' },
                    boundary: { width: isMobile ? 260 : 300, height: isMobile ? 260 : 300 }
                });

                crop.croppie('bind', { url: e.target.result });
                photoCropped = false;
            };
            reader.readAsDataURL(file);
        });

        $('#crop-btn').on('click', function () {
            if (!crop) return;
            crop.croppie('result', 'base64').then(function (img) {
                $('#cropped_image').val(img);
                $('#preview-img').attr('src', img);
                $('#crop-container').hide();
                $('#preview-wrap').css('display', 'flex');
                photoCropped = true;
                $('#err_photo').hide();
            });
        });

        $('#change-photo').on('click', function () {
            $('#preview-wrap').hide();
            $('#photoArea').show();
            $('#cropped_image').val('');
            $('#upload').val('');
            photoCropped = false;
        });

        // ── Submit validation ──
        $('#doctorForm').on('submit', function (e) {
            var valid = true;

            // Doctor — Select2 container pe error class
            if (!$('#doctor_id').val()) {
                $('#doctor_id').next('.select2-container').addClass('select2-error');
                $('#err_doctor').show();
                valid = false;
            } else {
                $('#doctor_id').next('.select2-container').removeClass('select2-error');
                $('#err_doctor').hide();
            }

            // Birth date
            if (!$('#birth_date').val()) {
                $('#birth_date').addClass('error'); $('#err_birth').show(); valid = false;
            } else {
                $('#birth_date').removeClass('error'); $('#err_birth').hide();
            }

            // Speciality
            if (!$('#speciality').val().trim()) {
                $('#speciality').addClass('error'); $('#err_speciality').show(); valid = false;
            } else {
                $('#speciality').removeClass('error'); $('#err_speciality').hide();
            }

            // Hospital
            if (!$('#hospital_name').val().trim()) {
                $('#hospital_name').addClass('error'); $('#err_hospital').show(); valid = false;
            } else {
                $('#hospital_name').removeClass('error'); $('#err_hospital').hide();
            }

            // Language — native select pe seedha error class
            if (!$('#language').val()) {
                $('#language').addClass('error'); $('#err_language').show(); valid = false;
            } else {
                $('#language').removeClass('error'); $('#err_language').hide();
            }

            // Photo
            if (!$('input[name="gender"]:checked').length) {
                $('#err_gender').show(); valid = false;
            } else {
                $('#err_gender').hide();
            }

            // Photo
            if (!photoCropped || !$('#cropped_image').val()) {
                $('#err_photo').show(); valid = false;
            } else {
                $('#err_photo').hide();
            }

            if (!valid) { e.preventDefault(); return false; }
        });
    </script>

@endsection
