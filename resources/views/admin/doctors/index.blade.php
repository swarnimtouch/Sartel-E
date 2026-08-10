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
                <i class="fas fa-download"></i> Download All Photos (ZIP)
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
                                <a href="https://swarnimpolling.s3.ap-south-1.amazonaws.com/{{ $doc->photo }}" target="_blank" download>
                                    <img src="https://swarnimpolling.s3.ap-south-1.amazonaws.com/{{ $doc->photo }}"
                                         width="45" height="45"
                                         style="border-radius:50%;object-fit:cover;cursor:pointer;border:2px solid var(--border);">
                                </a>
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
                        <td colspan="12">
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
                        <a href="https://swarnimpolling.s3.ap-south-1.amazonaws.com/{{ $doc->photo }}" target="_blank" download>
                            <img src="https://swarnimpolling.s3.ap-south-1.amazonaws.com/{{ $doc->photo }}"
                                 width="45" height="45"
                                 style="border-radius:50%;object-fit:cover;cursor:pointer;border:2px solid var(--border);">
                        </a>
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

@endsection
