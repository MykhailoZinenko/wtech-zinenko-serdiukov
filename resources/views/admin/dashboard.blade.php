@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="page-hdr">
    <div class="page-hdr__left">
        <h1 class="page-hdr__title">Dashboard</h1>
        <p class="page-hdr__sub">Welcome back, {{ auth()->user()->first_name }}.</p>
    </div>
</div>

<div class="adm-card">
    <div class="adm-card__hdr">
        <span class="adm-card__title">Account Information</span>
    </div>
    <div class="adm-card__body">
        <table class="adm-table">
            <tbody>
                <tr>
                    <td>Name</td>
                    <td>{{ auth()->user()->full_name }}</td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>{{ auth()->user()->email }}</td>
                </tr>
                <tr>
                    <td>Role</td>
                    <td><span class="badge badge--gold">{{ ucfirst(auth()->user()->role) }}</span></td>
                </tr>
                <tr>
                    <td>Member Since</td>
                    <td>{{ auth()->user()->created_at->format('F j, Y') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
