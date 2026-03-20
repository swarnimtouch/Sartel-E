@extends('layouts.app')

@section('title', 'Sartel-E || Add Doctor')
@section('page_title', 'Add Doctor')
@section('page_subtitle', 'Fill in the details to register a new doctor.')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.css">
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

                {{-- Select Doctor --}}
                <div class="form-group full">
                    <label>Select Doctor *</label>
                    <div class="custom-select-wrap">
                        <select id="doctor_id" name="doctor_id">
                            <option value="">Loading doctors...</option>
                        </select>
                    </div>
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

                <div class="form-group">
                    <label>Select Language *</label>
                    <div class="custom-select-wrap">
                        <select name="language" id="language" style="margin-top: 17px;">
                        <option value="">-- Select Language --</option>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.js"></script>
    <script>
        var crop         = null;
        var photoCropped = false;

        $(document).ready(function () {

            // ── Load unassigned doctors via AJAX ──
            $.ajax({
                url:  "{{ route('api.doctors_by_employee') }}",
                type: "GET",
                success: function (res) {
                    var $select = $('#doctor_id');
                    $select.empty().append('<option value="">-- Select Doctor --</option>');

                    if (res.length === 0) {
                        $('#no_doctors_msg').show();
                        $('#submitBtn').prop('disabled', true);
                        $select.prop('disabled', true);
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
                },
                error: function () {
                    $('#doctor_id')
                        .empty()
                        .append('<option value="">Failed to load doctors</option>')
                        .prop('disabled', true);
                }
            });

        });

        // ── Auto-fill MSL on doctor change ──
        $('#doctor_id').on('change', function () {
            var doctorId = $(this).val();

            if (doctorId) {
                $(this).removeClass('error');
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

            // Doctor
            if (!$('#doctor_id').val()) {
                $('#doctor_id').addClass('error'); $('#err_doctor').show(); valid = false;
            } else {
                $('#doctor_id').removeClass('error'); $('#err_doctor').hide();
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

            // Language
            if (!$('#language').val()) {
                $('#language').addClass('error'); $('#err_language').show(); valid = false;
            } else {
                $('#language').removeClass('error'); $('#err_language').hide();
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
