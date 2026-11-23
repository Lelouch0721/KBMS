@extends('layouts/blankLayout')

@section('title', 'Reset Password - Pages')

@section('page-style')
<!-- Page -->
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-auth.css')}}">
@endsection

@section('content')
<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-4">

      <!-- Reset Password -->
      <div class="card">
        <div class="card-body">
          <!-- Logo -->
          <div class="app-brand justify-content-center">
            <a href="{{url('/')}}" class="app-brand-link gap-2">
              <span class="app-brand-logo demo">@include('_partials.macros',["width"=>25,"withbg"=>'var(--bs-primary)'])</span>
              <span class="app-brand-text demo text-body fw-bold">{{ config('variables.templateName') }}</span>
            </a>
          </div>
          <!-- /Logo -->
          <h4 class="mb-2">Reset Password 🔒</h4>
          <p class="mb-4">Please enter your email address and your new password below.</p>

          <!-- Reset Password Form -->
          <form id="formAuthentication" class="mb-3" method="POST" action="{{ route('password.update') }}">
            @csrf <!-- CSRF token for form protection -->
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="mb-3">
              <label for="email" class="form-label">Email Address</label>
              <input type="email" class="form-control" id="email" name="email" value="{{ request('email') }}" required autofocus placeholder="Enter your email">
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">New Password</label>
              <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your new password">
            </div>
            <div class="mb-3">
              <label for="password_confirmation" class="form-label">Confirm New Password</label>
              <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="Confirm your new password">
            </div>
            <button class="btn btn-primary d-grid w-100">Reset Password</button>
          </form>
          
          <div class="text-center">
            <a href="{{ route('auth-login-basic') }}" class="d-flex align-items-center justify-content-center">
              <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i>
              Back to login
            </a>
          </div>
        </div>
      </div>
      <!-- /Reset Password -->
    </div>
  </div>
</div>
@endsection
