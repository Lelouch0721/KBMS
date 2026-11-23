@php
$containerNav = $containerNav ?? 'container-fluid';
$navbarDetached = $navbarDetached ?? '';
@endphp
<style>
  .layout-menu-toggle {
  position: relative; /* Ensures it stays within the normal document flow */
  z-index: 1050; /* Higher than most default z-index values */
  background-color: transparent; /* Removes any unwanted background */
}

</style>
<!-- Navbar -->
<nav class="layout-navbar {{ $containerNav }} navbar navbar-expand-xl {{ $navbarDetached ? $navbarDetached : 'bg-navbar-theme' }}" id="layout-navbar" style="{{ $navbarDetached ? 'z-index: 1030;' : 'z-index: 1030;' }}">
  @if(!$navbarDetached)
  <div class="{{ $containerNav }}">
  @endif

  <!-- Brand demo (display only for navbar-full and hide on below xl) -->
  @if(isset($navbarFull))
  <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
    <a href="{{ url('guest') }}" class="app-brand-link gap-2">
      <span class="app-brand-logo demo">@include('_partials.macros', ["width" => 25, "withbg" => 'var(--bs-primary)'])</span>
      <span class="app-brand-text demo menu-text fw-bold">{{ config('variables.templateName') }}</span>
    </a>
  </div>
  @endif

  @if(Auth::check() && Auth::user()->user_role !== 'User')
<div class="navbar-nav align-items-xl-center me-3 me-xl-0">
  <!-- Mobile View Logo -->
  <div class="d-block d-xl-none me-3">
    <a href="{{ url('dashboard') }}">
      <img src="{{ asset('assets/img/products/ccsit-logo.png') }}" alt="Logo" style="height: 40px; width: 40px;">
    </a>
  </div>
</div>
@endif


  
  <div class="app-brand demo">
    @if(Auth::check() && Auth::user()->user_role !== 'Admin')
        <a href="{{url('dashboard')}}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('assets/img/products/ccsit-logo.png') }}" alt="Logo" class="me-2" style="height: 50px; width: 50px;">
            </span>
        </a>
    @endif

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
        <i class="bx bx-chevron-left bx-sm align-middle"></i>
    </a>
</div>

  <div class="navbar-nav-right d-flex align-items-center w-100" id="navbar-collapse">
    <!-- Title -->
    <div class="navbar-nav d-flex flex-grow-1 justify-content-center">
      <div class="nav-item d-flex align-items-center">
        <h4 class="my-auto">Key Borrowing and Monitoring System</h4>
      </div>
    </div>
    <!-- /Title -->

    <ul class="navbar-nav flex-row align-items-center ms-auto">
      <!-- User -->
      <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <div class="avatar avatar-online">
            <img src="{{ asset('assets/img/products/profile.png') }}" alt class="w-px-40 h-auto rounded-circle">
          </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item" href="javascript:void(0);">
              <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                  <div class="avatar avatar-online">
                    <img src="{{ asset('assets/img/products/profile.png') }}" alt class="w-px-40 h-auto rounded-circle">
                  </div>
                </div>
                <div class="flex-grow-1">
                  <span class="fw-medium d-block text-capitalize">{{ Auth::user()->name }}</span>
                  <small class="text-muted text-capitalize">{{ Auth::user()->user_role }}</small>
                </div>
              </div>
            </a>
          </li>
          <li>
  <div class="dropdown-divider"></div>
  <a class="dropdown-item d-flex align-items-center" href="{{ route('qr.show') }}">
    <i class="bx bx-qr me-2"></i> <!-- Assuming you have a QR icon -->
    <span class="align-middle">Qr Code</span>
  </a>
</li>
<li>
  <form id="logout-form" action="{{ route('logout') }}" method="POST">
    @csrf
    <a class="dropdown-item d-flex align-items-center" href="javascript:void(0);" onclick="document.getElementById('logout-form').submit();">
      <i class="bx bx-power-off me-2"></i>
      <span class="align-middle">Log Out</span>
    </a>
  </form>
</li>

      <!-- /User -->
    </ul>
  </div>

  @if(!$navbarDetached)
  </div>
  @endif
</nav>
<!-- / Navbar -->
