@extends('layouts.admin')
@section('title', 'Sartel-E || Doctors')
@section('page-title', 'Doctors Directory')

@push('styles')
    <style>
        /* ── Mobile card view ── */
        .mobile-doctor-cards { display: none; }

        .doctor-mobile-card {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            transition: background .15s;
        }

        .doctor-mobile-card:last-child { border-bottom: none; }
        .doctor-mobile-card:hover { background: var(--surface2); }

        .doctor-mobile-card img,
        .doctor-mobile-card .avatar-placeholder {
            width: 46px; height: 46px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            border: 2px solid var(--border);
        }

        .doctor-mobile-card .avatar-placeholder {
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 700; color: #fff;
            border: none;
        }

        .dmc-info { flex: 1; min-width: 0; }

        .dmc-name {
            font-weight: 600;
            font-size: 14px;
            color: var(--text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dmc-row {
            font-size: 12px;
            color: var(--muted);
            margin-top: 3px;
            display: flex;
            flex-wrap: wrap;
            gap: 6px 14px;
        }

        .dmc-row span { display: flex; align-items: center; gap: 5px; }

        .dmc-badge {
            display: inline-block;
            margin-top: 6px;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 500;
            background: rgba(59,130,246,.12);
            color: var(--accent);
        }

        /* ── Responsive filters ── */
        @media (max-width: 768px) {
            .filters-bar {
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
            }

            .search-box { min-width: unset; width: 100%; }
            .filter-select { width: 100%; }

            .filters-bar .btn { width: 100%; justify-content: center; }

            /* Hide desktop table, show mobile cards */
            .table-wrap { display: none; }
            .mobile-doctor-cards { display: block; }

            .pagination-wrap {
                flex-direction: column;
                align-items: center;
                gap: 10px;
                text-align: center;
            }

            .pagination { justify-content: center; }
        }
        .pagination-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 20px;
            flex-wrap: wrap;
            gap: 10px;
            padding: 0 4px;
        }

        .pagination-info {
            font-size: .83rem;
            color: #94a3b8;
        }

        .pagination-links {
            display: flex;
            gap: 6px;
            align-items: center;
        }

        .page-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px; height: 34px;
            border-radius: 8px;
            font-size: .85rem;
            font-weight: 600;
            color: #94a3b8;
            background: #1e293b;
            border: 1.5px solid #334155;
            text-decoration: none;
            transition: all .2s;
        }

        .page-btn:hover:not(.disabled):not(.active) {
            background: #1d4ed8;
            border-color: #3b82f6;
            color: white;
        }

        .page-btn.active {
            background: #3b82f6;
            border-color: transparent;
            color: white;
        }

        .page-btn.disabled {
            opacity: .35;
            cursor: not-allowed;
        }

        /* ── Export button ── */
        .btn-success {
            background: #16a34a;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: .875rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            text-decoration: none;
            transition: background .2s;
        }
        .btn-success:hover { background: #15803d; color: #fff; }

        .admin-flash {
            margin: 0 0 16px;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
        }

        .admin-flash-success { color: #a7f3d0; background: rgba(16,185,129,.14); border: 1px solid rgba(16,185,129,.35); }
        .admin-flash-error { color: #fecaca; background: rgba(239,68,68,.14); border: 1px solid rgba(239,68,68,.35); }

        .btn-reset-doctor {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 34px;
            height: 34px;
            padding: 0;
            border: 1px solid rgba(239, 68, 68, .5);
            border-radius: 8px;
            color: #fca5a5;
            background: rgba(239, 68, 68, .1);
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
        }

        .btn-reset-doctor:hover { color: #fff; background: #dc2626; }

        .doctor-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: nowrap;
        }

        .btn-banner-generate {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 13px;
            border: 1px solid rgba(59, 130, 246, 0.6);
            border-radius: 8px;
            color: #ffffff;
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
            transition: all 0.2s ease;
        }

        .btn-banner-generate:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.4);
            filter: brightness(1.1);
        }

        .btn-banner-generate:disabled {
            opacity: 0.45;
            cursor: not-allowed;
            box-shadow: none;
            filter: grayscale(0.8);
        }

        .btn-banner-generated {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 13px;
            border: 1px solid rgba(16, 185, 129, 0.6);
            border-radius: 8px;
            color: #ffffff;
            background: linear-gradient(135deg, #059669, #10b981);
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .btn-banner-generated:hover {
            transform: translateY(-1px);
            color: #ffffff;
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
            filter: brightness(1.08);
        }

        .btn-banner-regenerate {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            padding: 0;
            border: 1px solid rgba(148, 163, 184, 0.4);
            border-radius: 8px;
            color: #94a3b8;
            background: rgba(30, 41, 59, 0.8);
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-banner-regenerate:hover {
            color: #ffffff;
            background: #334155;
            border-color: #64748b;
            transform: translateY(-1px);
        }

        .image-preview-trigger {
            padding: 0;
            border: 0;
            background: transparent;
            cursor: pointer;
        }

        .banner-preview-cell {
            display: flex;
            align-items: center;
        }

        .preview-banner-button {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 14px;
            border: 1px solid rgba(96, 165, 250, .7);
            border-radius: 999px;
            color: #fff;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
            box-shadow: 0 5px 15px rgba(59, 130, 246, .25);
            transition: transform .2s, box-shadow .2s, filter .2s;
        }

        .preview-banner-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(124, 58, 237, .35);
            filter: brightness(1.08);
        }

        .image-preview-modal {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: rgba(2, 6, 23, .88);
        }

        .image-preview-modal.open { display: flex; }

        .image-preview-dialog {
            position: relative;
            max-width: min(920px, 96vw);
            max-height: 94vh;
            padding: 48px 18px 18px;
            border-radius: 14px;
            background: var(--surface);
            box-shadow: 0 24px 80px rgba(0, 0, 0, .5);
        }

        .image-preview-dialog img {
            display: block;
            max-width: 100%;
            max-height: 78vh;
            object-fit: contain;
            border-radius: 8px;
        }

        .image-preview-close {
            position: absolute;
            top: 10px;
            right: 12px;
            width: 32px;
            height: 32px;
            border: 0;
            border-radius: 50%;
            color: #fff;
            background: #334155;
            cursor: pointer;
            font-size: 20px;
        }
    </style>
@endpush

@section('content')

    @if(session('success'))
        <div class="admin-flash admin-flash-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="admin-flash admin-flash-error">{{ session('error') }}</div>
    @endif

    <div class="card">

        {{-- Header --}}
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div>
                <div class="card-title">All Doctors</div>
                <div class="card-sub">{{ $doctors->total() }} Doctors Found</div>
            </div>
            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <a href="{{ route('admin.employee.import') }}"
                   class="btn"
                   style="background: var(--surface2); color: var(--text); border: 1px solid var(--border); padding: 8px 14px; border-radius: 8px; font-size: .875rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; transition: background .2s;">
                    <i class="fas fa-file-upload"></i> Import Employees
                </a>

                <a href="{{ route('admin.doctors.download-generated-photos', request()->query()) }}"
                   class="btn"
                   style="background: linear-gradient(135deg, #2563eb, #7c3aed); color: #fff; border: none; padding: 8px 16px; border-radius: 8px; font-size: .875rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; box-shadow: 0 4px 14px rgba(37, 99, 235, .3); transition: transform .2s, filter .2s;"
                   onmouseover="this.style.transform='translateY(-1px)'; this.style.filter='brightness(1.1)';"
                   onmouseout="this.style.transform='none'; this.style.filter='none';">
                    <i class="fas fa-file-archive"></i>
                    @if(request('zone'))
                        Download {{ request('zone') }} Generated (ZIP)
                    @else
                        Download Generated Photos (ZIP)
                    @endif
                </a>

                <a href="{{ route('admin.doctors.download-photos', request()->query()) }}"
                   class="btn btn-success">
                    <i class="fas fa-download"></i>
                    @if(request('zone'))
                        Download {{ request('zone') }} Photos &amp; Banners (ZIP)
                    @else
                        Download Photos &amp; Banners (ZIP)
                    @endif
                </a>

                {{-- ── EXPORT BUTTON (top-right) ── --}}
                <a href="{{ route('admin.doctors.export') }}?{{ http_build_query(request()->only(['search', 'zone', 'speciality'])) }}"
                   class="btn btn-success">
                    <i class="fas fa-file-excel"></i>
                    <span>Export Excel</span>
                </a>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.doctors.index') }}">
            <div class="filters-bar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search"
                           placeholder="Search by name, code or speciality..."
                           value="{{ request('search') }}">
                </div>

                <div class="filter-select-wrap">
                    <select name="zone" class="filter-select" onchange="this.form.submit()">
                        <option value="">All Zones</option>
                        @foreach($zones as $z)
                            <option value="{{ $z }}" {{ request('zone') === $z ? 'selected' : '' }}>
                                Zone: {{ $z }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if(isset($specialities) && $specialities->isNotEmpty())
                    <div class="filter-select-wrap">
                        <select name="speciality" class="filter-select" onchange="this.form.submit()">
                            <option value="">All Specialities</option>
                            @foreach($specialities as $spec)
                                <option value="{{ $spec }}" {{ request('speciality') === $spec ? 'selected' : '' }}>
                                    {{ $spec }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> <span>Filter</span>
                </button>

                @if(request()->hasAny(['search', 'city', 'speciality', 'zone']))
                    <a href="{{ route('admin.doctors.index') }}" class="btn btn-ghost">
                        <i class="fas fa-times"></i> <span>Reset</span>
                    </a>
                @endif
            </div>
        </form>

        {{-- ── DESKTOP TABLE ── --}}
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Photo</th>
                    <th>Action</th>
                    <th>Doctor</th>
                    <th>Doctor Msl Code</th>
                    <th>Language</th>
                    <th>Gender</th>
                    <th>Banner</th>
                    <th>Zone</th>
                    <th>Employee Name</th>
                    <th>Employee Code</th>
                    <th>Speciality</th>
                    <th>Hospital</th>
                    <th>Birth Date</th>
                    <th>Updated</th>
                </tr>
                </thead>
                <tbody>
                @forelse($doctors as $doc)
                    <tr>
                        <td>{{ $doctors->firstItem() + $loop->index }}</td>
                        <td>
                            @if($doc->photo)
                                <button type="button" class="image-preview-trigger"
                                        data-image="https://swarnimpolling.s3.ap-south-1.amazonaws.com/{{ $doc->photo }}"
                                        data-alt="{{ $doc->doctor_name }} photo">
                                    <img src="https://swarnimpolling.s3.ap-south-1.amazonaws.com/{{ $doc->photo }}"
                                         width="45" height="45"
                                         style="border-radius:50%;object-fit:cover;cursor:pointer;border:2px solid var(--border);">
                                </button>
                            @else
                                <div class="avatar-placeholder">
                                    {{ strtoupper(substr($doc->doctor_name, 0, 1)) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="doctor-actions">
                                @if($doc->is_generated && $doc->banner_path)
                                    <a href="{{ route('admin.doctors.download-banner', $doc) }}"
                                       class="btn-banner-generated"
                                       title="Download generated banner">
                                        <i class="fas fa-download"></i> Generated
                                    </a>
                                    <button type="button"
                                            class="btn-banner-regenerate btn-generate-trigger"
                                            data-url="{{ route('admin.doctors.generate-banner', $doc) }}"
                                            data-name="{{ $doc->doctor_name }}"
                                            title="Regenerate Banner">
                                        <i class="fas fa-redo-alt"></i>
                                    </button>
                                @else
                                    <button type="button"
                                            class="btn-banner-generate btn-generate-trigger"
                                            data-url="{{ route('admin.doctors.generate-banner', $doc) }}"
                                            data-name="{{ $doc->doctor_name }}"
                                            {{ !$doc->photo ? 'disabled' : '' }}
                                            title="{{ !$doc->photo ? 'Doctor photo required to generate' : 'Generate AI Banner' }}">
                                        <i class="fas fa-wand-magic-sparkles"></i> Generate
                                    </button>
                                @endif

                                <form method="POST" action="{{ route('admin.doctors.reset', $doc) }}"
                                      class="reset-doctor-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-reset-doctor" title="Delete submitted data"
                                            aria-label="Delete submitted data for {{ $doc->doctor_name }}">
                                        <i class="fas fa-trash-alt" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                        <td>
                            <div class="doctor-name">{{ $doc->doctor_name }}</div>
                        </td>
                        <td>
                            <div class="doctor-name">{{ $doc->msl_code }}</div>
                        </td>
                        <td>
                            <div class="doctor-name">{{ $doc->language }}</div>
                        </td>
                        <td>{{ $doc->gender ?? '-' }}</td>
                        <td>
                            @if($doc->banner_path)
                                <div class="banner-preview-cell">
                                    <button type="button" class="image-preview-trigger preview-banner-button"
                                            data-image="https://swarnimpolling.s3.ap-south-1.amazonaws.com/{{ $doc->banner_path }}?v={{ optional($doc->updated_at)->timestamp }}"
                                            data-alt="{{ $doc->doctor_name }} banner">
                                        <i class="fas fa-eye"></i> Preview Banner
                                    </button>
                                </div>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($doc->employee?->zone)
                                <span class="badge" style="background: rgba(147, 51, 234, 0.15); color: #c084fc; border: 1px solid rgba(147, 51, 234, 0.3); font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 6px;">
                                    {{ $doc->employee->zone }}
                                </span>
                            @else
                                <span style="color: var(--muted); font-size: 12px;">-</span>
                            @endif
                        </td>
                        <td>{{ $doc->employee->name ?? '-' }}</td>
                        <td>
                            <span class="doctor-id">{{ $doc->employee->employee_code ?? '-' }}</span>
                        </td>
                        <td>
                            @if($doc->speciality)
                                <span class="badge badge-blue">{{ $doc->speciality }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $doc->hospital_name ?? '-' }}</td>
                        <td>{{ $doc->birth_date ?? '-' }}</td>
                        <td>{{ optional($doc->updated_at)->format('d M Y') ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="15">
                            <div class="empty-state">
                                <i class="fas fa-user-md"></i>
                                <p>No doctors found. Please adjust your filters.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- ── MOBILE CARDS ── --}}
        <div class="mobile-doctor-cards">
            @forelse($doctors as $doc)
                <div class="doctor-mobile-card">
                    @if($doc->photo)
                        <button type="button" class="image-preview-trigger"
                                data-image="https://swarnimpolling.s3.ap-south-1.amazonaws.com/{{ $doc->photo }}"
                                data-alt="{{ $doc->doctor_name }} photo">
                            <img src="https://swarnimpolling.s3.ap-south-1.amazonaws.com/{{ $doc->photo }}"
                                 width="45" height="45"
                                 style="border-radius:50%;object-fit:cover;cursor:pointer;border:2px solid var(--border);">
                        </button>
                    @else
                        <div class="avatar-placeholder">
                            {{ strtoupper(substr($doc->doctor_name, 0, 1)) }}
                        </div>
                    @endif

                    <div class="dmc-info">
                        <div class="dmc-name">{{ $doc->doctor_name }}</div>
                        <div class="dmc-name">{{ $doc->msl_code }}</div>
                        <div class="dmc-name">{{ $doc->language }}</div>
                        <div class="dmc-name">{{ $doc->gender ?? '-' }}</div>
                        @if($doc->banner_path)
                            <div class="dmc-row">
                                <div class="banner-preview-cell">
                                    <button type="button" class="image-preview-trigger preview-banner-button"
                                            data-image="https://swarnimpolling.s3.ap-south-1.amazonaws.com/{{ $doc->banner_path }}?v={{ optional($doc->updated_at)->timestamp }}"
                                            data-alt="{{ $doc->doctor_name }} banner">
                                        <i class="fas fa-eye"></i> Preview Banner
                                    </button>
                                </div>
                            </div>
                        @endif

                        <div class="dmc-row">
                            @if($doc->employee?->zone)
                                <span><i class="fas fa-map-marker-alt" style="font-size:10px; color:#c084fc;"></i> <strong style="color:#c084fc;">Zone: {{ $doc->employee->zone }}</strong></span>
                            @endif
                            <span><i class="fas fa-user" style="font-size:10px;"></i> {{ $doc->employee->name ?? '-' }}</span>
                            <span><i class="fas fa-id-badge" style="font-size:10px;"></i> {{ $doc->employee->employee_code ?? '-' }}</span>
                            <span><i class="fas fa-hospital" style="font-size:10px;"></i> {{ $doc->hospital_name ?? '-' }}</span>
                        </div>

                        <div class="dmc-row">
                            <span><i class="fas fa-cake-candles" style="font-size:10px;"></i> {{ $doc->birth_date ?? '-' }}</span>
                            <span><i class="fas fa-calendar" style="font-size:10px;"></i> {{ optional($doc->created_at)->format('d M Y') ?? '-' }}</span>
                        </div>

                        @if($doc->speciality)
                            <span class="dmc-badge">{{ $doc->speciality }}</span>
                        @endif

                        <div class="dmc-row doctor-actions" style="margin-top: 10px;">
                            @if($doc->is_generated && $doc->banner_path)
                                <a href="{{ route('admin.doctors.download-banner', $doc) }}"
                                   class="btn-banner-generated"
                                   title="Download generated banner">
                                    <i class="fas fa-download"></i> Generated
                                </a>
                                <button type="button"
                                        class="btn-banner-regenerate btn-generate-trigger"
                                        data-url="{{ route('admin.doctors.generate-banner', $doc) }}"
                                        data-name="{{ $doc->doctor_name }}"
                                        title="Regenerate Banner">
                                    <i class="fas fa-redo-alt"></i>
                                </button>
                            @else
                                <button type="button"
                                        class="btn-banner-generate btn-generate-trigger"
                                        data-url="{{ route('admin.doctors.generate-banner', $doc) }}"
                                        data-name="{{ $doc->doctor_name }}"
                                        {{ !$doc->photo ? 'disabled' : '' }}
                                        title="{{ !$doc->photo ? 'Doctor photo required to generate' : 'Generate AI Banner' }}">
                                    <i class="fas fa-wand-magic-sparkles"></i> Generate
                                </button>
                            @endif

                            <form method="POST" action="{{ route('admin.doctors.reset', $doc) }}"
                                  class="reset-doctor-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-reset-doctor" title="Delete submitted data"
                                        aria-label="Delete submitted data for {{ $doc->doctor_name }}">
                                    <i class="fas fa-trash-alt" aria-hidden="true"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-user-md"></i>
                    <p>No doctors found. Please adjust your filters.</p>
                </div>
            @endforelse
        </div>

        {{-- ── PAGINATION ── --}}
        @if($doctors->hasPages())
            <div class="pagination-wrap">
                <div class="pagination-info">
                    Showing {{ $doctors->firstItem() }}–{{ $doctors->lastItem() }} of {{ $doctors->total() }} doctors
                </div>

                <div class="pagination-links">

                    @if($doctors->onFirstPage())
                        <span class="page-btn disabled">‹</span>
                    @else
                        <a href="{{ $doctors->previousPageUrl() }}" class="page-btn">‹</a>
                    @endif

                    @foreach($doctors->getUrlRange(1, $doctors->lastPage()) as $page => $url)
                        @if($page == $doctors->currentPage())
                            <span class="page-btn active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($doctors->hasMorePages())
                        <a href="{{ $doctors->nextPageUrl() }}" class="page-btn">›</a>
                    @else
                        <span class="page-btn disabled">›</span>
                    @endif

                </div>
            </div>
        @endif

    </div>

    <div class="image-preview-modal" id="imagePreviewModal" role="dialog" aria-modal="true" aria-hidden="true">
        <div class="image-preview-dialog">
            <button type="button" class="image-preview-close" aria-label="Close preview">&times;</button>
            <img id="imagePreviewFull" src="" alt="">
        </div>
    </div>

    <script>
        (() => {
            const modal = document.getElementById('imagePreviewModal');
            const fullImage = document.getElementById('imagePreviewFull');

            document.querySelectorAll('.image-preview-trigger').forEach((trigger) => {
                trigger.addEventListener('click', () => {
                    fullImage.src = trigger.dataset.image;
                    fullImage.alt = trigger.dataset.alt || 'Image preview';
                    modal.classList.add('open');
                    modal.setAttribute('aria-hidden', 'false');
                });
            });

            const closePreview = () => {
                modal.classList.remove('open');
                modal.setAttribute('aria-hidden', 'true');
                fullImage.src = '';
            };

            modal.querySelector('.image-preview-close').addEventListener('click', closePreview);
            modal.addEventListener('click', (event) => {
                if (event.target === modal) closePreview();
            });
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && modal.classList.contains('open')) closePreview();
            });
        })();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.reset-doctor-form').forEach((form) => {
            form.addEventListener('submit', (event) => {
                event.preventDefault();

                Swal.fire({
                    titleText: 'Delete submitted data?',
                    text: 'The photo and banner will be deleted from S3. Doctor name and MSL code will be preserved.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="fas fa-trash-alt"></i> Delete',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });

        // ── Banner Generation Handler ──
        document.querySelectorAll('.btn-generate-trigger').forEach((btn) => {
            btn.addEventListener('click', async function () {
                const url = this.dataset.url;
                const doctorName = this.dataset.name || 'Doctor';
                const isRegen = this.classList.contains('btn-banner-regenerate');

                const confirmResult = await Swal.fire({
                    titleText: isRegen ? 'Regenerate AI Banner?' : 'Generate AI Banner?',
                    html: `This will perform an AI face swap for <strong>${doctorName}</strong> and save the banner to S3.<br><small style="color:#94a3b8;">OpenAI face swap takes ~30-40 seconds</small>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: isRegen ? '<i class="fas fa-redo-alt"></i> Yes, Regenerate' : '<i class="fas fa-wand-magic-sparkles"></i> Yes, Generate',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                });

                if (!confirmResult.isConfirmed) return;

                // Show processing indicator
                Swal.fire({
                    title: 'Generating Banner...',
                    html: `Generating AI face swap banner for <strong>${doctorName}</strong>...<br><span style="font-size:12px;color:#94a3b8;">Please wait, do not refresh or close this tab.</span>`,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                        || '{{ csrf_token() }}';

                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        await Swal.fire({
                            icon: 'success',
                            titleText: 'Banner Generated!',
                            html: `AI Face-swap banner generated and saved to S3 successfully for <strong>${doctorName}</strong>.`,
                            confirmButtonColor: '#10b981',
                            confirmButtonText: '<i class="fas fa-check"></i> Great!',
                        });
                        window.location.reload();
                    } else {
                        throw new Error(data.message || 'Banner generation failed.');
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        titleText: 'Generation Failed',
                        text: error.message || 'An unexpected error occurred while generating the banner.',
                        confirmButtonColor: '#dc2626',
                    });
                }
            });
        });
    </script>

@endsection
