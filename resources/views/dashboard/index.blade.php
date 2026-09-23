@extends('layouts.app')

@php
    $rVal = $role->value ?? (string)$role;
    $roleTitle = match($rVal) {
        'superadmin' => 'Superadmin (Platform Owner)',
        'company_admin' => 'Company Admin (Admin Tenant)',
        'agent' => 'Teknisi (IT Specialist Workspace)',
        'requester' => 'Portal Layanan Mandiri (Self-Service)',
        default => 'Dashboard Utama',
    };
@endphp

@section('title', 'Dashboard ' . $roleTitle . ' - SiriusTicketing')
@section('page_title', 'Dashboard ' . $roleTitle)

@section('toolbar_actions')
    <div class="d-flex align-items-center gap-2">
        @if($rVal === 'requester')
            <a href="{{ url('/tickets') }}" class="btn btn-sm btn-primary rounded-3 shadow-xs">
                <i class="ki-duotone ki-plus fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
                Ajukan Tiket Baru
            </a>
        @elseif($rVal === 'superadmin')
            <a href="{{ url('/companies') }}" class="btn btn-sm btn-primary rounded-3 shadow-xs">
                <i class="ki-duotone ki-plus fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
                Kelola Perusahaan
            </a>
        @else
            <a href="{{ url('/tickets') }}" class="btn btn-sm btn-light-primary rounded-3 shadow-xs">
                <i class="ki-duotone ki-tablet-text-down fs-4 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                Buka Antrean Tiket
            </a>
        @endif
    </div>
@endsection

@section('content')
    @if($rVal === 'superadmin')
        @include('dashboard._superadmin', ['data' => $superadmin])
    @elseif($rVal === 'company_admin')
        @include('dashboard._company_admin', ['data' => $companyAdmin])
    @elseif($rVal === 'agent')
        @include('dashboard._agent', ['data' => $agent])
    @elseif($rVal === 'requester')
        @include('dashboard._requester', ['data' => $requester])
    @endif
@endsection
