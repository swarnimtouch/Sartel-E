@extends('layouts.app')

@section('title', 'Manage Doctors')
@section('page_title', 'Manage Doctors')
@section('page_subtitle', 'View and manage all registered doctors.')

@push('styles')
    <style>
        /* ── Alert ── */
        .alert-success {
            background: #dcfce7;
            color: #16a34a;
            padding: 12px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
            font-size: .9rem;
        }

        /* ── Card header ── */
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 12px;
            flex-wrap: wrap;
        }

        .card-header h3 {
            font-size: 1rem;
            font-weight: 700;
            color: #0f172a;
        }

        .btn-add {
            padding: 9px 18px;
            background: linear-gradient(90deg, #38bdf8, #818cf8);
            color: white;
            border-radius: 10px;
            text-decoration: none;
            font-size: .85rem;
            font-weight: 600;
            white-space: nowrap;
            transition: opacity .2s;
        }

        .btn-add:hover { opacity: .88; }

        /* ── Table wrapper ── */
        .table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin: 0 -24px;
            padding: 0 24px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: .88rem;
            min-width: 600px;
        }

        thead tr {
            border-bottom: 2px solid #f1f5f9;
        }

        thead th {
            text-align: left;
            padding: 10px 12px;
            color: #64748b;
            font-weight: 600;
            white-space: nowrap;
        }

        tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background .15s;
        }

        tbody tr:hover { background: #f8fafc; }
        tbody tr:last-child { border-bottom: none; }

        td { padding: 12px; vertical-align: middle; }

        .doctor-avatar {
            width: 48px; height: 48px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e2e8f0;
            display: block;
        }

        .avatar-placeholder {
            width: 48px; height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #38bdf8, #818cf8);
            display: grid;
            place-items: center;
            color: white;
            font-weight: 700;
            font-size: 1rem;
        }

        .doctor-name { font-weight: 500; color: #0f172a; }
        .text-muted  { color: #64748b; }
        .text-light  { color: #94a3b8; }

        /* ── Pagination ── */
        .pagination-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 20px;
            gap: 12px;
            flex-wrap: wrap;
        }

        .pagination-info {
            font-size: .82rem;
            color: #64748b;
        }

        .pagination {
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
        }

        .pagination a,
        .pagination span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 10px;
            border-radius: 8px;
            font-size: .82rem;
            font-weight: 500;
            text-decoration: none;
            border: 1.5px solid #e2e8f0;
            color: #64748b;
            background: #fff;
            transition: all .2s;
        }

        .pagination a:hover {
            border-color: #38bdf8;
            color: #38bdf8;
            background: #f0f9ff;
        }

        .pagination .active > span,
        .pagination [aria-current="page"] > span {
            background: linear-gradient(90deg, #38bdf8, #818cf8);
            color: #fff;
            border-color: transparent;
        }

        .pagination [disabled] span,
        .pagination span.disabled {
            opacity: .4;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* ── Mobile card view ── */
        .mobile-cards { display: none; }

        @media (max-width: 640px) {
            .table-wrap { display: none; }
            .mobile-cards { display: flex; flex-direction: column; gap: 12px; }

            .doctor-card {
                display: flex;
                align-items: center;
                gap: 14px;
                padding: 14px;
                border: 1.5px solid #f1f5f9;
                border-radius: 12px;
                background: #f8fafc;
            }

            .doctor-card-info { flex: 1; min-width: 0; }

            .doctor-card-info .name {
                font-weight: 600;
                font-size: .92rem;
                color: #0f172a;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .doctor-card-info .meta {
                font-size: .78rem;
                color: #64748b;
                margin-top: 3px;
            }

            .doctor-card-info .badge {
                display: inline-block;
                margin-top: 6px;
                padding: 2px 10px;
                border-radius: 20px;
                font-size: .72rem;
                font-weight: 600;
                background: #e0f2fe;
                color: #0284c7;
            }

            .pagination-info { width: 100%; text-align: center; }
            .pagination { justify-content: center; width: 100%; }
        }
        .pagination-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .pagination-info {
            font-size: .83rem;
            color: #64748b;
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
            color: #475569;
            background: #f1f5f9;
            border: 1.5px solid #e2e8f0;
            text-decoration: none;
            transition: all .2s;
        }

        .page-btn:hover:not(.disabled):not(.active) {
            background: #e0f2fe;
            border-color: #38bdf8;
            color: #0284c7;
        }

        .page-btn.active {
            background: linear-gradient(90deg, #38bdf8, #818cf8);
            border-color: transparent;
            color: white;
        }

        .page-btn.disabled {
            opacity: .4;
            cursor: not-allowed;
        }
    </style>
@endpush

@section('content')

    @if(session('success'))
        <div class="alert-success">✅ {{ session('success') }}</div>
    @endif

    <div class="card">

        <div class="card-header">
            <h3>All Doctors</h3>
            <a href="{{ route('doctors.create') }}" class="btn-add">+ Add Doctor</a>
        </div>

        {{-- ── DESKTOP TABLE ── --}}
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Language</th>
                    <th>Gender</th>
                    <th>MSL Code</th>
                    <th>Speciality</th>
                    <th>Hospital</th>
                    <th>Birth Date</th>
                    <th>Actions</th>  {{-- ← YEH ADD KARO --}}

                </tr>
                </thead>
                <tbody>
                @forelse($doctors as $doctor)
                    <tr>
                        <td class="text-light">{{ $doctors->firstItem() + $loop->index }}</td>
                        <td>
                            @if($doctor->photo)
                                <img src="https://swarnimpolling.s3.ap-south-1.amazonaws.com/{{ $doctor->photo }}" class="doctor-avatar">
                            @endif
                        </td>
                        <td class="doctor-name">{{ $doctor->doctor_name }}</td>
                        <td class="text-muted">{{ $doctor->language ?? '-' }}</td>
                        <td class="text-muted">{{ $doctor->gender ?? '-' }}</td>
                        <td class="text-muted">{{ $doctor->msl_code ?? '-' }}</td>
                        <td class="text-muted">{{ $doctor->speciality ?? '-' }}</td>
                        <td class="text-muted">{{ $doctor->hospital_name ?? '-' }}</td>
                        <td class="text-muted">{{ $doctor->birth_date ?? '-' }}</td>
                        <td>  {{-- ← YEH POORA TD ADD KARO --}}
                            <a href="{{ route('doctors.edit', $doctor->id) }}"
                               style="display:inline-flex;align-items:center;gap:5px;
                      padding:6px 14px;border-radius:8px;font-size:.78rem;
                      font-weight:600;text-decoration:none;
                      background:#f0f9ff;color:#0284c7;border:1.5px solid #bae6fd;
                      transition:all .2s;"
                               onmouseover="this.style.background='#0284c7';this.style.color='#fff'"
                               onmouseout="this.style.background='#f0f9ff';this.style.color='#0284c7'">
                                ✏️ Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="padding:40px;text-align:center;color:#94a3b8;">
                            No doctors found. <a href="{{ route('doctors.create') }}" style="color:#38bdf8;font-weight:600;">Add your first doctor!</a>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- ── MOBILE CARDS ── --}}
        <div class="mobile-cards">
            @forelse($doctors as $doctor)
                <div class="doctor-card">
                    @if($doctor->photo)
                        <img src="https://swarnimpolling.s3.ap-south-1.amazonaws.com/{{ $doctor->photo }}" class="doctor-avatar" alt="{{ $doctor->doctor_name }}">
                    @else
                        <div class="avatar-placeholder">
                            {{ strtoupper(substr($doctor->doctor_name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="doctor-card-info">
                        <div class="name">{{ $doctor->doctor_name }}</div>
                        <div class="name">{{ $doctor->msl_code }}</div>
                        <div class="name">{{ $doctor->language }}</div>
                        <div class="name">{{ $doctor->gender ?? '-' }}</div>
                        <div class="meta">
                            🏥 {{ $doctor->hospital_name ?? 'N/A' }}
                        </div>
                        <div class="meta">
                            🎂 {{ $doctor->birth_date ?? 'N/A' }}
                        </div>
                        @if($doctor->speciality)
                            <span class="badge">{{ $doctor->speciality }}</span>
                        @endif
                        <div style="margin-top:8px;">
                            <a href="{{ route('doctors.edit', $doctor->id) }}"
                               style="display:inline-flex;align-items:center;gap:5px;
              padding:5px 12px;border-radius:8px;font-size:.75rem;
              font-weight:600;text-decoration:none;
              background:#f0f9ff;color:#0284c7;border:1.5px solid #bae6fd;">
                                ✏️ Edit
                            </a>
                        </div>

                    </div>
                </div>
            @empty
                <div style="text-align:center;padding:32px;color:#94a3b8;font-size:.88rem;">
                    No doctors found. <a href="{{ route('doctors.create') }}" style="color:#38bdf8;font-weight:600;">Add your first!</a>
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

                    {{-- Previous --}}
                    @if($doctors->onFirstPage())
                        <span class="page-btn disabled">‹</span>
                    @else
                        <a href="{{ $doctors->previousPageUrl() }}" class="page-btn">‹</a>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach($doctors->getUrlRange(1, $doctors->lastPage()) as $page => $url)
                        @if($page == $doctors->currentPage())
                            <span class="page-btn active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Next --}}
                    @if($doctors->hasMorePages())
                        <a href="{{ $doctors->nextPageUrl() }}" class="page-btn">›</a>
                    @else
                        <span class="page-btn disabled">›</span>
                    @endif

                </div>
            </div>
        @endif

    </div>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.delete-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                var url  = this.getAttribute('href');
                var name = this.getAttribute('data-name');

                Swal.fire({
                    title: 'Delete Doctor?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f43f5e',
                    cancelButtonColor:  '#94a3b8',
                    confirmButtonText: 'Delete!',
                    cancelButtonText:  'Cancel',
                }).then(function(result) {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });
    </script>

@endsection
