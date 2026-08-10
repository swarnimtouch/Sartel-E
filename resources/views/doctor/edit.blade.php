@extends('layouts.app')

@section('title', 'Edit Doctor')
@section('page_title', 'Edit Doctor')
@section('page_subtitle', 'Update the doctor\'s information below.')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.css">
    <style>
        .form-card {
            max-width: 700px;
        }

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

        .form-group.full {
            grid-column: 1 / -1;
        }

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
            box-shadow: 0 0 0 3px rgba(56, 189, 248, .12);
        }

        .form-group input[readonly] {
            background: #f1f5f9;
            color: #94a3b8;
            cursor: not-allowed;
            border-color: #e2e8f0;
        }

        .form-group input.error {
            border-color: #f43f5e;
            background-color: #fff1f2;
        }

        /* ── Custom Select Wrapper ── */
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
            box-shadow: 0 0 0 3px rgba(56, 189, 248, .12);
        }

        .custom-select-wrap select.error {
            border-color: #f43f5e;
            background-color: #fff1f2;
        }

        .custom-select-wrap select:disabled {
            opacity: .55;
            cursor: not-allowed;
        }

        /* Custom chevron */
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

        /* ── Readonly badge ── */
        .readonly-badge {
            display: inline-block;
            font-size: .68rem;
            font-weight: 600;
            background: #e2e8f0;
            color: #94a3b8;
            padding: 1px 7px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: .04em;
            margin-left: 6px;
            vertical-align: middle;
        }

        /* ── Error messages ── */
        .err-msg {
            font-size: .78rem;
            color: #f43f5e;
            font-weight: 500;
            display: none;
        }

        /* ── Current photo strip ── */
        .current-photo {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 14px 16px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            margin-bottom: 12px;
        }

        .current-photo img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #38bdf8;
            flex-shrink: 0;
        }

        .current-photo .no-photo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #38bdf8, #818cf8);
            display: grid;
            place-items: center;
            color: white;
            font-weight: 700;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .current-photo-info p {
            font-size: .88rem;
            font-weight: 600;
            color: #0f172a;
            margin: 0 0 2px;
        }

        .current-photo-info span {
            font-size: .78rem;
            color: #94a3b8;
        }

        /* ── Photo upload area ── */
        .photo-area {
            border: 2px dashed #cbd5e1;
            border-radius: 14px;
            padding: 24px;
            text-align: center;
            background: #f8fafc;
            transition: border .2s;
        }

        .photo-area:hover {
            border-color: #38bdf8;
        }

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

        #crop {
            width: 300px;
            height: 300px;
            margin: 0 auto;
        }

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

        /* ── New photo preview ── */
        #preview-wrap {
            display: none;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            margin-top: 16px;
        }

        #preview-wrap img {
            width: 90px;
            height: 90px;
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

        .submit-btn:hover {
            opacity: .9;
        }

        .cancel-link {
            color: #64748b;
            font-size: .88rem;
            text-decoration: none;
            font-weight: 500;
        }

        /* ── MOBILE ── */
        @media (max-width: 600px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: 1;
            }

            #crop {
                width: 260px;
                height: 260px;
            }

            .photo-area {
                padding: 18px 14px;
            }

            .submit-btn {
                width: 100%;
                text-align: center;
            }

            .form-actions {
                flex-direction: column;
                gap: 10px;
            }

            .cancel-link {
                width: 100%;
                text-align: center;
            }
        }
    </style>
@endpush

@section('content')

    <div class="card form-card">

        <form method="POST" action="{{ route('doctors.update', $doctor->id) }}" id="doctorForm">
            @csrf

            <input type="hidden" name="cropped_image" id="cropped_image">

            <div class="form-grid">

                {{-- Doctor Name — READ ONLY --}}
                <div class="form-group full">
                    <label>Doctor Name </label>
                    <input type="text" name="doctor_name" id="doctor_name"
                           value="{{ $doctor->doctor_name }}">
                </div>

                {{-- MSL Code — READ ONLY --}}
                <div class="form-group">
                    <label>MSL Code <span class="readonly-badge">Read Only</span></label>
                    <input type="text" name="msl_code"
                           value="{{ $doctor->msl_code ?? '-' }}" readonly>
                </div>

                {{-- Birth Date --}}
                <div class="form-group">
                    <label>Birth Date</label>
                    <input type="date" name="birth_date" id="birth_date"
                           value="{{ old('birth_date', $doctor->birth_date) }}">
                    <span class="err-msg" id="err_birth">Birth date is required.</span>
                </div>

                {{-- Speciality --}}
                <div class="form-group">
                    <label>Speciality *<br>(as per the doctor's visiting card)</label>
                    <input type="text" name="speciality" id="speciality"
                           placeholder="e.g. Cardiologist"
                           value="{{ old('speciality', $doctor->speciality) }}">
                    <span class="err-msg" id="err_speciality">Speciality is required.</span>
                </div>

                {{-- Language --}}
                <div class="form-group">
                    <label>Language *</label>
                    <div class="custom-select-wrap">
                        <select name="language" id="language" style="margin-top: 17px;">
                            <option value="">-- Select Language --</option>
                            <option
                                value="English" {{ old('language', $doctor->language) == 'English'     ? 'selected' : '' }}>
                                English
                            </option>
                            <option
                                value="Hindi" {{ old('language', $doctor->language) == 'Hindi'     ? 'selected' : '' }}>
                                Hindi
                            </option>
                            <option
                                value="Bengali" {{ old('language', $doctor->language) == 'Bengali'   ? 'selected' : '' }}>
                                Bengali
                            </option>
                            <option
                                value="Gujarati" {{ old('language', $doctor->language) == 'Gujarati'  ? 'selected' : '' }}>
                                Gujarati
                            </option>
                            <option
                                value="Marathi" {{ old('language', $doctor->language) == 'Marathi'   ? 'selected' : '' }}>
                                Marathi
                            </option>
                            <option
                                value="Telugu" {{ old('language', $doctor->language) == 'Telugu'    ? 'selected' : '' }}>
                                Telugu
                            </option>
                            <option
                                value="Tamil" {{ old('language', $doctor->language) == 'Tamil'     ? 'selected' : '' }}>
                                Tamil
                            </option>
                            <option
                                value="Odia" {{ old('language', $doctor->language) == 'Odia'      ? 'selected' : '' }}>
                                Odia
                            </option>
                            <option
                                value="Punjabi" {{ old('language', $doctor->language) == 'Punjabi'   ? 'selected' : '' }}>
                                Punjabi
                            </option>
                            <option
                                value="Assamese" {{ old('language', $doctor->language) == 'Assamese'  ? 'selected' : '' }}>
                                Assamese
                            </option>
                            <option
                                value="Kannada" {{ old('language', $doctor->language) == 'Kannada'   ? 'selected' : '' }}>
                                Kannada
                            </option>
                            <option
                                value="Malayalam" {{ old('language', $doctor->language) == 'Malayalam' ? 'selected' : '' }}>
                                Malayalam
                            </option>
                        </select>
                    </div>
                    <span class="err-msg" id="err_language">Language is required.</span>
                </div>

                {{-- Hospital Name --}}
                <div class="form-group">
                    <label>Hospital Name *<br>(as per the doctor's visiting card)</label>
                    <input type="text" name="hospital_name" id="hospital_name"
                           placeholder="e.g. City Hospital"
                           value="{{ old('hospital_name', $doctor->hospital_name) }}">
                    <span class="err-msg" id="err_hospital">Hospital name is required.</span>
                </div>

                <div class="form-group full">
                    <label>Gender *</label>
                    <div style="display:flex;gap:24px;flex-wrap:wrap;padding-top:10px;">
                        @foreach(['Male', 'Female'] as $gender)
                            <label style="display:flex;align-items:center;gap:7px;text-transform:none;letter-spacing:0;cursor:pointer;">
                                <input type="radio" name="gender" value="{{ $gender }}"
                                       style="width:auto;" {{ old('gender', $doctor->gender) === $gender ? 'checked' : '' }}>
                                {{ $gender }}
                            </label>
                        @endforeach
                    </div>
                    <span class="err-msg" id="err_gender">Please select a gender.</span>
                    @error('gender')<span class="err-msg" style="display:block;">{{ $message }}</span>@enderror
                </div>

                {{-- Photo --}}
                <div class="form-group full">
                    <label>Doctor Photo
                        <span
                            style="color:#94a3b8;text-transform:none;letter-spacing:0;font-weight:400;font-size:.8rem;">
                            (optional — change only if needed)
                        </span>
                    </label>

                    {{-- Current photo --}}
                    <div class="current-photo">
                        @if($doctor->photo)
                            <img src="https://swarnimpolling.s3.ap-south-1.amazonaws.com/{{ $doctor->photo }}"
                                 alt="Current Photo">
                        @else
                            <div class="no-photo">{{ strtoupper(substr($doctor->doctor_name, 0, 1)) }}</div>
                        @endif
                        <div class="current-photo-info">
                            <p>Current Photo</p>
                            <span>Upload a new photo below to replace it</span>
                        </div>
                    </div>

                    {{-- Upload new --}}
                    <div class="photo-area" id="photoArea">
                        <label for="upload" class="upload-label">📷 Choose New Photo</label>
                        <input type="file" id="upload" accept="image/*" style="display:none">
                        <p>JPG, PNG supported • Leave empty to keep current photo</p>
                    </div>

                    {{-- Croppie --}}
                    <div id="crop-container">
                        <div id="crop"></div>
                        <button type="button" id="crop-btn">✂️ Crop &amp; Use Photo</button>
                    </div>

                    {{-- New preview --}}
                    <div id="preview-wrap">
                        <img id="preview-img" src="" alt="New Preview">
                        <button type="button" id="change-photo">🔄 Change Again</button>
                    </div>
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="submit-btn">Update Doctor</button>
                <a href="{{ route('doctors.index') }}" class="cancel-link">Cancel</a>
            </div>

        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.js"></script>
    <script>
        var crop = null;

        // ── Croppie ──
        $('#upload').on('change', function () {
            var file = this.files[0];
            if (!file) return;

            var reader = new FileReader();
            reader.onload = function (e) {
                $('#photoArea').hide();
                $('#preview-wrap').hide().css('display', 'none');
                $('#crop-container').show();

                if (crop) {
                    crop.croppie('destroy');
                    crop = null;
                }

                var isMobile = window.innerWidth <= 600;
                crop = $('#crop').croppie({
                    viewport: {width: isMobile ? 180 : 200, height: isMobile ? 180 : 200, type: 'circle'},
                    boundary: {width: isMobile ? 260 : 300, height: isMobile ? 260 : 300}
                });

                crop.croppie('bind', {url: e.target.result});
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
            });
        });

        $('#change-photo').on('click', function () {
            $('#preview-wrap').hide();
            $('#photoArea').show();
            $('#cropped_image').val('');
            $('#upload').val('');
        });

        // ── Live clear errors ──
        $('#speciality').on('input', function () {
            if ($(this).val().trim()) {
                $(this).removeClass('error');
                $('#err_speciality').hide();
            }
        });

        $('#hospital_name').on('input', function () {
            if ($(this).val().trim()) {
                $(this).removeClass('error');
                $('#err_hospital').hide();
            }
        });

        $('#language').on('change', function () {
            if ($(this).val()) {
                $(this).removeClass('error');
                $('#err_language').hide();
            }
        });

        $('#birth_date').on('change', function () {
            if ($(this).val()) {
                $(this).removeClass('error');
                $('#err_birth').hide();
            }
        });

        // ── Submit validation ──
        $('#doctorForm').on('submit', function (e) {
            var valid = true;

            // Speciality
            if (!$('#speciality').val().trim()) {
                $('#speciality').addClass('error');
                $('#err_speciality').show();
                valid = false;
            } else {
                $('#speciality').removeClass('error');
                $('#err_speciality').hide();
            }

            // Hospital
            if (!$('#hospital_name').val().trim()) {
                $('#hospital_name').addClass('error');
                $('#err_hospital').show();
                valid = false;
            } else {
                $('#hospital_name').removeClass('error');
                $('#err_hospital').hide();
            }

            // Language
            if (!$('#language').val()) {
                $('#language').addClass('error');
                $('#err_language').show();
                valid = false;
            } else {
                $('#language').removeClass('error');
                $('#err_language').hide();
            }

            // Birth date
            if (!$('input[name="gender"]:checked').length) {
                $('#err_gender').show(); valid = false;
            } else {
                $('#err_gender').hide();
            }

            // Birth date
            if (!$('#birth_date').val()) {
                $('#birth_date').addClass('error');
                $('#err_birth').show();
                valid = false;
            } else {
                $('#birth_date').removeClass('error');
                $('#err_birth').hide();
            }

            if (!valid) {
                e.preventDefault();
                return false;
            }
        });
    </script>

@endsection
