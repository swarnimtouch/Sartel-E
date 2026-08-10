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

    <div class="card">

        {{-- Header --}}
        <div class="card-header">
            <div>
                <div class="card-title">All Doctors</div>
                <div class="card-sub">{{ $doctors->total() }} Doctors Found</div>
            </div>
            <a href="{{ route('admin.doctors.download-photos', request()->query()) }}"
               class="btn btn-success">
                <i class="fas fa-download"></i> Download Photos &amp; Banners (ZIP)
            </a>

            {{-- ── EXPORT BUTTON (top-right) ── --}}
            <a href="{{ route('admin.doctors.export') }}?{{ http_build_query(request()->only(['search'])) }}"
               class="btn btn-success">
                <i class="fas fa-file-excel"></i>
                <span>Export Excel</span>
            </a>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.doctors.index') }}">
            <div class="filters-bar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search"
                           placeholder="Search by name or speciality..."
                           value="{{ request('search') }}">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> <span>Filter</span>
                </button>

                @if(request()->hasAny(['search', 'city', 'speciality']))
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
                    <th>Doctor</th>
                    <th>Doctor Msl Code</th>
                    <th>Language</th>
                    <th>Gender</th>
                    <th>Banner</th>
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
                                            data-image="https://swarnimpolling.s3.ap-south-1.amazonaws.com/{{ $doc->banner_path }}"
                                            data-alt="{{ $doc->doctor_name }} banner">
                                        <i class="fas fa-eye"></i> Preview Banner
                                    </button>
                                </div>
                            @else
                                -
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
                        <td colspan="13">
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
                                            data-image="https://swarnimpolling.s3.ap-south-1.amazonaws.com/{{ $doc->banner_path }}"
                                            data-alt="{{ $doc->doctor_name }} banner">
                                        <i class="fas fa-eye"></i> Preview Banner
                                    </button>
                                </div>
                            </div>
                        @endif

                        <div class="dmc-row">
                            <span><i class="fas fa-user" style="font-size:10px;"></i> {{ $doc->employee->name ?? '-' }}</span>
                            <span><i class="fas fa-user" style="font-size:10px;"></i> {{ $doc->employee->employee_code ?? '-' }}</span>
                            <span><i class="fas fa-hospital" style="font-size:10px;"></i> {{ $doc->hospital_name ?? '-' }}</span>
                        </div>

                        <div class="dmc-row">
                            <span><i class="fas fa-cake-candles" style="font-size:10px;"></i> {{ $doc->birth_date ?? '-' }}</span>
                            <span><i class="fas fa-calendar" style="font-size:10px;"></i> {{ optional($doc->created_at)->format('d M Y') ?? '-' }}</span>
                        </div>

                        @if($doc->speciality)
                            <span class="dmc-badge">{{ $doc->speciality }}</span>
                        @endif
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

@endsection
