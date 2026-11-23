@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard')

@section('vendor-style')
@endsection

@section('vendor-script')
@endsection

@section('page-style')
<style>
  .swal2-popup-custom-zindex {
      z-index: 9999 !important;
  }

  .page-background {
    background-color: #f8f9fa;
    color: #333;
  }

  .welcome-section {
    text-align: center;
    padding: 20px;
    color: #333;
  }

  .explore-btn {
    color: #fff;
    padding: 8px 16px;
    text-decoration: none;
    border-radius: 4px;
    background-color: #007bff;
    border: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: background-color 0.3s, transform 0.3s;
  }

  .explore-btn:hover {
    background-color: #0056b3;
    transform: scale(1.02);
  }

  .intro-text {
    font-size: 1em;
    margin: 10px 0;
    color: #333;
  }

  .card {
    border-radius: 6px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s, box-shadow 0.3s;
  }

  .card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  }

  .card.bg-primary {
    background-color: #007bff;
    color: #fff;
  }

  .card.bg-dark {
    background-color: #343a40;
    color: #fff;
  }

  .card.bg-warning {
    background-color: #ffc107;
    color: #333;
  }

  .card.bg-info {
    background-color: #17a2b8;
    color: #fff;
  }

  .card.bg-success {
    background-color: #28a745;
    color: #fff;
  }

  .card.bg-black {
    background-color: #000;
    color: #fff;
  }

  .card .icon {
    color: #fff;
    background-color: rgba(0, 0, 0, 0.1);
  }

  .card.bg-primary .icon {
    background-color: #0056b3;
  }

  .card.bg-dark .icon {
    background-color: #495057;
  }

  .card.bg-warning .icon {
    background-color: #e0a800;
  }

  .card.bg-info .icon {
    background-color: #117a8b;
  }

  .card.bg-success .icon {
    background-color: #1c7430;
  }

  .card.bg-black .icon {
    background-color: #333;
  }

  .card-body .text-muted {
    color: #6c757d;
  }

  .card-body h3 {
    color: #fff;
    font-size: 1.5em;
  }

  .card-body h6 {
    color: #fff;
    font-size: 1em;
  }

  .card.bg-dark h3, .card.bg-dark h6 {
    color: #fff;
  }

  @media (max-width: 768px) {
    .card-body h3 {
      font-size: 1.3em;
    }

    .card-body h6 {
      font-size: 0.9em;
    }
  }
</style>
@endsection

@section('content')
<div class="container page-background">
    <div class="row">
        <!-- Admin Count Card -->
        <div class="col-lg-3 col-md-6 mb-3">
            <a href="{{ route('user.view') }}" class="card-link">
                <div class="card bg-primary">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon rounded-circle p-2">
                            <i class="fas fa-user-shield"></i> <!-- Admin Icon -->
                        </div>
                        <div class="ms-2">
                            <h6>Admins</h6>
                            <h3>{{ $data['admin'] }}</h3>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <!-- User Count Card -->
        <div class="col-lg-3 col-md-6 mb-3">
            <a href="{{ route('user.view') }}" class="card-link">
                <div class="card bg-dark">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon rounded-circle p-2">
                            <i class="fas fa-users"></i> <!-- User Icon -->
                        </div>
                        <div class="ms-2">
                            <h6>Users</h6>
                            <h3>{{ $data['user'] }}</h3>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <!-- Key Count Card -->
        <div class="col-lg-3 col-md-6 mb-3">
            <a href="{{ route('key.view') }}" class="card-link">
                <div class="card bg-warning">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon rounded-circle p-2">
                            <i class="bx bx-plus-circle"></i> <!-- Key Icon -->
                        </div>
                        <div class="ms-2">
                            <h6>Manage Items</h6>
                            <h3>{{ $data['item'] }}</h3>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <!-- Borrowed Keys Count Card -->
        <div class="col-lg-3 col-md-6 mb-3">
            <a href="{{ route('borrows.view') }}" class="card-link">
                <div class="card bg-success">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon rounded-circle p-2">
                            <i class="bx bx-box"></i> <!-- Borrowed Keys Icon -->
                        </div>
                        <div class="ms-2">
                            <h6>Borrowed Items</h6>
                            <h3>{{ $data['borrowedKeys'] }}</h3>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <!-- Returned Keys Count Card -->
        <div class="col-lg-3 col-md-6 mb-3">
            <a href="{{ route('history.view') }}" class="card-link">
                <div class="card bg-info">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon rounded-circle p-2">
                            <i class="bx bx-left-arrow-alt"></i> <!-- Returned Keys Icon -->
                        </div>
                        <div class="ms-2">
                            <h6>Returned Items</h6>
                            <h3>{{ $data['returnedKeys'] }}</h3>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <!-- User App Card -->
        <div class="col-lg-3 col-md-6 mb-3">
            <a href="{{ route('guest') }}" class="card-link">
                <div class="card bg-black">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon rounded-circle p-2">
                            <i class="bx bx-mobile"></i>
                        </div>
                        <div class="ms-2">
                            <h6>Access User App</h6>
                            <h3>{{ $data['borrowedKeys'] }}</h3>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
