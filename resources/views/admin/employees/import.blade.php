@extends('layouts.admin')
@section('title', 'Sartel-E || Import Employees')
@section('page-title', 'Import Employees')

@push('styles')
    <style>
        .import-card {
            max-width: 720px;
            margin: 0 auto;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .2);
        }

        .import-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 8px;
        }

        .import-sub {
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 24px;
            line-height: 1.6;
        }

        .drop-area {
            border: 2px dashed var(--border);
            border-radius: 12px;
            padding: 36px 20px;
            text-align: center;
            background: var(--surface2);
            transition: border-color .2s, background .2s;
            cursor: pointer;
            margin-bottom: 24px;
            position: relative;
        }

        .drop-area:hover, .drop-area.dragover {
            border-color: var(--accent);
            background: rgba(59, 130, 246, .05);
        }

        .drop-area input[type="file"] {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .drop-icon {
            font-size: 40px;
            color: var(--accent);
            margin-bottom: 12px;
        }

        .drop-text {
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 4px;
        }

        .drop-sub {
            font-size: 12px;
            color: var(--muted);
        }

        .selected-file-name {
            margin-top: 10px;
            font-size: 13px;
            font-weight: 600;
            color: #38bdf8;
            word-break: break-all;
        }

        .info-box {
            background: rgba(59, 130, 246, .08);
            border: 1px solid rgba(59, 130, 246, .25);
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 24px;
            font-size: 12.5px;
            color: #93c5fd;
            line-height: 1.6;
        }

        .info-box strong { color: #fff; }
        .info-box code {
            background: rgba(0, 0, 0, .3);
            padding: 2px 6px;
            border-radius: 4px;
            color: #67e8f9;
            font-family: 'JetBrains Mono', monospace;
        }

        .btn-submit-import {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all .2s;
            box-shadow: 0 4px 14px rgba(37, 99, 235, .3);
        }

        .btn-submit-import:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
        }

        .btn-back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--muted);
            font-size: 13px;
            text-decoration: none;
            margin-bottom: 16px;
            transition: color .2s;
        }
        .btn-back-link:hover { color: var(--text); }
    </style>
@endpush

@section('content')

    <a href="{{ route('admin.doctors.index') }}" class="btn-back-link">
        <i class="fas fa-arrow-left"></i> Back to Doctors
    </a>

    @if(session('success'))
        <div class="admin-flash admin-flash-success" style="margin-bottom: 20px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="admin-flash admin-flash-error" style="margin-bottom: 20px;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="admin-flash admin-flash-error" style="margin-bottom: 20px;">
            <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
        </div>
    @endif

    <div class="import-card">
        <div class="import-title">
            <i class="fas fa-users-cog" style="color: var(--accent); margin-right: 8px;"></i> Import Employees Sheet
        </div>
        <div class="import-sub">
            Upload your Excel or CSV sheet to insert new employees or update existing ones with their <strong>Zone</strong>.
        </div>

        <div class="info-box">
            <strong>Expected Sheet Columns (Headers):</strong><br>
            <code>zone</code>, <code>position_code</code>, <code>employee_code</code>, <code>name</code>, <code>designation_name</code>, <code>hq_name</code>, <code>hq_code</code><br>
            <span style="color: #cbd5e1; font-size: 11.5px;">* Agar employee pehle se exist karta hai to uska <strong>zone</strong> update ho jayega.</span>
        </div>

        <form action="{{ route('admin.employee.import.post') }}" method="POST" enctype="multipart/form-data" id="employeeImportForm">
            @csrf

            <div class="drop-area" id="dropArea">
                <i class="fas fa-cloud-arrow-up drop-icon"></i>
                <div class="drop-text">Click or drag &amp; drop your sheet here</div>
                <div class="drop-sub">Supported formats: .xlsx, .xls, .csv</div>
                <input type="file" name="file" id="fileInput" accept=".xlsx,.xls,.csv" required>
                <div class="selected-file-name" id="fileNameDisplay" style="display: none;"></div>
            </div>

            <button type="submit" class="btn-submit-import" id="submitBtn">
                <i class="fas fa-file-import"></i> Upload &amp; Import Employees
            </button>
        </form>
    </div>

    <script>
        const fileInput = document.getElementById('fileInput');
        const fileNameDisplay = document.getElementById('fileNameDisplay');
        const dropArea = document.getElementById('dropArea');

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                fileNameDisplay.textContent = 'Selected: ' + fileInput.files[0].name;
                fileNameDisplay.style.display = 'block';
            } else {
                fileNameDisplay.style.display = 'none';
            }
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropArea.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropArea.classList.remove('dragover');
            }, false);
        });
    </script>

@endsection
