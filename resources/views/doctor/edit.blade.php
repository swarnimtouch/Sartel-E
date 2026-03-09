@extends('layouts.app')

@section('title', 'Edit Doctor')
@section('page_title', 'Edit Doctor')
@section('page_subtitle', 'Update the doctor\'s information below.')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.css">
    <style>
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        @media (max-width: 600px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            .form-group.full {
                grid-column: 1;
            }
            .card {
                padding: 16px !important;
            }
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

        .form-group input {
            padding: 11px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: .9rem;
            font-family: inherit;
            color: #0f172a;
            transition: border .2s;
            outline: none;
            background: #f8fafc;
        }

        .form-group input:focus {
            border-color: #38bdf8;
            background: #fff;
        }

        .form-group input.error {
            border-color: #f43f5e;
            background: #fff1f2;
        }

        /* Read-only fields */
        .form-group input[readonly] {
            background: #f1f5f9;
            color: #64748b;
            cursor: not-allowed;
            border-color: #e2e8f0;
        }

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

        .err-msg {
            font-size: .78rem;
            color: #f43f5e;
            font-weight: 500;
            display: none;
        }

        /* Current photo */
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

        /* Photo upload area */
        .photo-area {
            border: 2px dashed #cbd5e1;
            border-radius: 14px;
            padding: 20px;
            text-align: center;
            background: #f8fafc;
            transition: border .2s;
        }

        .photo-area:hover { border-color: #38bdf8; }

        .photo-area label {
            display: inline-block;
            padding: 9px 20px;
            background: linear-gradient(90deg,#38bdf8,#818cf8);
            color: white;
            border-radius: 10px;
            font-size: .85rem;
            font-weight: 600;
            cursor: pointer;
            text-transform: none !important;
            letter-spacing: 0 !important;
        }

        .photo-area p {
            color: #94a3b8;
            font-size: .78rem;
            margin-top: 6px;
        }

        #crop-container {
            display: none;
            margin-top: 20px;
        }

        #crop {
            width: 300px;
            height: 300px;
            margin: 0 auto;
            max-width: 100%;
        }

        #crop-btn {
            display: block;
            margin: 49px auto 0;
            padding: 10px 28px;
            background: #0f172a;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: .88rem;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
        }

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
        }

        .form-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 24px;
            flex-wrap: wrap;
        }

        .submit-btn {
            padding: 13px 32px;
            background: linear-gradient(90deg,#38bdf8,#818cf8);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: .95rem;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            transition: opacity .2s;
        }

        .submit-btn:hover { opacity: .9; }

        .cancel-link {
            color: #64748b;
            font-size: .88rem;
            text-decoration: none;
            font-weight: 500;
        }

        @media (max-width: 400px) {
            .submit-btn { width: 100%; }
            .cancel-link { display: block; text-align: center; }
            #crop { width: 240px; height: 240px; }
        }
    </style>
@endpush

@section('content')

    <div class="card" style="max-width:700px;">

        <form method="POST" action="{{ route('doctors.update', $doctor->id) }}" id="doctorForm">
            @csrf

            <input type="hidden" name="cropped_image" id="cropped_image">

            <div class="form-grid">

                {{-- Doctor Name — READ ONLY --}}
                <div class="form-group full">
                    <label>Doctor Name <span class="readonly-badge">Read Only</span></label>
                    <input type="text" name="doctor_name" id="doctor_name"
                           value="{{ $doctor->doctor_name }}" readonly>
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
                    <input type="date" name="birth_date"
                           value="{{ old('birth_date', $doctor->birth_date) }}">
                </div>


                <div class="form-group">
                    <label>Speciality</label>
                    <input type="text" name="speciality" id="speciality"
                           placeholder="e.g. Cardiologist"
                           value="{{ old('birth_date', $doctor->speciality) }}">
                </div>

                <div class="form-group">
                    <label>Hospital Name</label>
                    <input type="text" name="hospital_name" id="hospital_name"
                           placeholder="e.g. Cardiologist"
                           value="{{ old('birth_date', $doctor->hospital_name) }}">
                </div>

                {{-- Photo --}}
                <div class="form-group full">
                    <label>Doctor Photo
                        <span style="color:#94a3b8;text-transform:none;letter-spacing:0;font-weight:400;">
                            (optional — change only if needed)
                        </span>
                    </label>

                    {{-- Current photo preview --}}
                    <div class="current-photo">
                        <img src="https://swarnimpolling.s3.ap-south-1.amazonaws.com/{{ $doctor->photo }}" alt="Current Photo">

                        <div class="current-photo-info">
                            <p>Current Photo</p>
                            <span>Upload new photo below to replace it</span>
                        </div>
                    </div>

                    {{-- Upload new --}}
                    <div class="photo-area" id="photoArea">
                        <label for="upload">📷 Choose New Photo</label>
                        <input type="file" id="upload" accept="image/*" style="display:none">
                        <p>Leave empty to keep current photo</p>
                    </div>

                    {{-- Croppie --}}
                    <div id="crop-container">
                        <div id="crop"></div>
                        <button type="button" id="crop-btn">✂️ Crop &amp; Use Photo</button>
                    </div>

                    {{-- New preview --}}
                    <div id="preview-wrap">
                        <img id="preview-img" src="">
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

        $('#upload').on('change', function () {
            var file = this.files[0];
            if (!file) return;

            var reader = new FileReader();
            reader.onload = function (e) {
                $('#photoArea').hide();
                $('#preview-wrap').hide().css('display','none');
                $('#crop-container').show();

                if (crop) { crop.croppie('destroy'); crop = null; }

                crop = $('#crop').croppie({
                    viewport: { width: 200, height: 200, type: 'circle' },
                    boundary: { width: 300, height: 300 }
                });

                crop.croppie('bind', { url: e.target.result });
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

        // Validation on submit
        $('#doctorForm').on('submit', function (e) {
            var valid = true;

            // Mobile optional but must be 10 digits if filled
            var mobile = $('#mobile').val().trim();
            if (mobile && !/^\d{10}$/.test(mobile)) {
                $('#mobile').addClass('error');
                $('#err_mobile').show();
                valid = false;
            } else {
                $('#mobile').removeClass('error');
                $('#err_mobile').hide();
            }

            if (!valid) {
                e.preventDefault();
                return false;
            }
        });

        $('#mobile').on('input', function () {
            var v = $(this).val().trim();
            if (!v || /^\d{10}$/.test(v)) {
                $(this).removeClass('error');
                $('#err_mobile').hide();
            }
        });
    </script>

@endsection
