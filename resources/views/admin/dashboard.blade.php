@extends('layouts.admin')
@section('title', 'Sartel-E || Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
    <style>
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 12px;
                margin-bottom: 20px;
            }

            .stat-card {
                padding: 16px;
            }

            .stat-icon {
                width: 36px;
                height: 36px;
                font-size: 15px;
                margin-bottom: 10px;
                border-radius: 10px;
            }

            .stat-value {
                font-size: 22px;
            }

            .stat-label {
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')

    <div class="stats-grid">

        <div class="stat-card blue">
            <div class="stat-icon blue"><i class="fas fa-user-md"></i></div>
            <div class="stat-value">{{ $totalDoctors }}</div>
            <div class="stat-label">Total Doctors</div>
        </div>

    </div>

@endsection
