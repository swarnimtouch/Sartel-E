@extends('layouts.app')

@section('title', 'Sartel-E || Dashboard')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Welcome back! Here\'s what\'s happening today.')

@push('styles')
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 28px;
        }

        .stat-card {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            display: grid;
            place-items: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.1;
        }

        .stat-label {
            font-size: .82rem;
            color: #64748b;
            font-weight: 500;
            margin-top: 3px;
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 12px;
            }

            .stat-card {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
                padding: 16px !important;
            }

            .stat-icon {
                width: 44px;
                height: 44px;
                font-size: 1.2rem;
                border-radius: 10px;
            }

            .stat-value {
                font-size: 1.5rem;
            }
        }
    </style>
@endpush

@section('content')

    <div class="stats-grid">

        <div class="card stat-card">
            <div class="stat-icon">👨‍⚕️</div>
            <div>
                <div class="stat-value">{{ $doctor_count }}</div>
                <div class="stat-label">Total Doctors</div>
            </div>
        </div>

    </div>

@endsection
