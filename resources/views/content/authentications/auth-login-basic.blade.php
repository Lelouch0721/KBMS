@extends('layouts/blankLayout')

@section('title', 'Login Basic - Pages')

@section('page-style')
<style>
  body, html {
    height: 100%;
    background-color: #c9d6ff;
    background: linear-gradient(to right, #e2e2e2, #c9d6ff);
    display: flex;
    align-items: flex-start;
    justify-content: center;
    margin: 0;
  }

  .container-xxl {
    margin-top: 10px;
  }

  .authentication-wrapper {
    display: flex;
    max-width: 1000px;
    width: 100%;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.15);
    overflow: hidden;
    flex-direction: row;
  }

  .auth-left, .auth-right {
    flex: 1;
    padding: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .auth-left {
    background-color: #ffffff;
    padding: 50px;
    flex-direction: column;
    text-align: left;
  }

  .auth-left h2, .auth-left h3 {
    font-size: 25px;
    font-weight: bold;
  }

  .auth-right {
    background-color: #f0f2f5;
    flex-direction: column;
  }

  .auth-right img {
    height: auto;
    max-width: 100%;
    margin: 0 auto;
  }

  @media (max-width: 768px) {
    .authentication-wrapper {
      flex-direction: column;
    }
    

    .auth-left, .auth-right {
      width: 100%;
      padding: 20px;
    }

    .auth-left {
      text-align: center;
    }

    .auth-left h2, .auth-left h3 {
      font-size: 24px;
    }
  }

  .google-signin-container {
    display: flex;
    justify-content: center;
    margin-top: 20px;
  }

  .btn-primary {
    background-color: #007bff;
    border-color: #007bff;
  }

  .btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
  }

  .input-group-text {
    background-color: transparent;
    border-left: 0;
  }

  .input-group input {
    border-right: 0;
  }

  .spinner-border {
    margin-left: 10px;
  }

  .form-check-label {
    margin-left: 5px;
  }
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
      @if(session('success'))
          Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: '{{ session('success') }}',
              timer: 3000,
              showConfirmButton: false
          });
      @elseif(session('error'))
          Swal.fire({
              icon: 'error',
              title: 'Error!',
              text: '{{ session('error') }}',
              timer: 3000,
              showConfirmButton: false
          });
      @endif
  });
</script>
@endsection

@section('content')
<div class="container-xxl">
  <div class="authentication-wrapper">
    <div class="auth-left">
      <div class="card shadow-none">
        <div class="card-body">
          <h2 class="mb-2">Key Borrowing</h2>
          <h3 class="mb-2">and</h3>
          <h2 class="mb-4">Monitoring System</h2>
          <div id="errorMessage" class="alert alert-danger" style="display: none;"></div>
          <form id="formAuthentication" class="mb-3" method="POST" action="{{ route('store') }}">
            @csrf
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" autofocus>
            </div>
            <div class="mb-3 form-password-toggle">
              <div class="d-flex justify-content-between">
                <label class="form-label" for="password">Password</label>
                <a href="{{url('auth/forgot-password-basic')}}">
                  <small>Forgot Password?</small>
                </a>
              </div>
              <div class="input-group input-group-merge">
                <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
              </div>
            </div>
            <button id="signinBtn" class="btn btn-primary d-grid w-100" type="submit">
              <span id="signinBtnText">Sign in</span>
              <span id="signinBtnLoader" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            </button>
          </form>
          <p class="text-center">
            <span>New on our platform?</span>
            <a href="{{url('auth/register-basic')}}">
              <span>Create an account</span>
            </a>
          </p>
          <p class= "text-center">
          <span>or sign with:</span>
          <div class="google-signin-container">
            <div class="g_id_signin" data-type="standard"></div>
          </div>
          <div id="g_id_onload" data-client_id="{{env('GOOGLE_CLIENT_ID')}}" data-callback="onSignIn"></div>
        </div>
      </div>
    </div>

    <div class="auth-right">
      <img src="{{ asset('assets/img/products/ccsit-logo.png') }}" alt="Logo">
    </div>
  </div>
</div>
@endsection

<script src="https://accounts.google.com/gsi/client" async defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
$(document).ready(function () {
    $('#formAuthentication').on('submit', function (e) {
        e.preventDefault();

        const email = $('#email').val().trim();
        const password = $('#password').val().trim();

        if (!email || !password) {
            Swal.fire({
                icon: 'error',
                title: 'Empty Fields!',
                text: "Please don't leave empty fields",
                showConfirmButton: true,
                confirmButtonText: 'OK'
            });
            return;
        }

        $('#signinBtn').prop('disabled', true).html('Signing in...');

        const formData = $(this).serialize();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{ route('store') }}",
            type: "POST",
            data: formData,
            success: function (response) {
                if (response.role === 'Admin') {
                    window.location.href = "{{ route('dashboard-analytics') }}";
                } else if (response.role === 'User') {
                    window.location.href = "{{ route('guest') }}";
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Role',
                        text: 'The user role is invalid.',
                        showConfirmButton: true,
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function (xhr) {
                $('#signinBtn').prop('disabled', false).html('Sign in');

                let errorMsg = 'An error occurred. Please try again.';

                if (xhr.status === 401) {
                    errorMsg = 'Invalid email or password.';
                } else if (xhr.status === 403) {
                    errorMsg = xhr.responseJSON?.message || 'Account not approved by an admin.';
                } else if (xhr.status === 429) {
                    const retryAfter = xhr.getResponseHeader('Retry-After');
                    errorMsg = retryAfter
                        ? `Too many requests. Please try again in ${retryAfter} seconds.`
                        : 'Too many requests. Please try again later.';
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMsg,
                    showConfirmButton: true,
                    confirmButtonText: 'OK'
                });
            }
        });
    });
});

function decodeJwtResponse(token) {
    try {
        let base64Url = token.split('.')[1];
        let base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
        let jsonPayload = decodeURIComponent(atob(base64).split('').map(function (c) {
            return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
        }).join(''));
        return JSON.parse(jsonPayload);
    } catch (error) {
        return null;
    }
}

window.onSignIn = function (googleUser) {
    var user = decodeJwtResponse(googleUser.credential);
    if (user) {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $.ajax({
            url: '/google',
            method: 'POST',
            data: { email: user.email },
            beforeSend: function () {
                $('#signinBtn').html("SIGNING IN...").prop("disabled", true);
            },
            success: function (response) {
                if (response.message === "success") {
                    if (response.role === 'Admin') {
                        window.location.href = "{{ route('dashboard-analytics') }}";
                    } else if (response.role === 'User') {
                        window.location.href = "{{ route('guest') }}";
                    }
                } else if (response.message === "not_approved") {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Account Not Approved',
                        text: 'Your account has not been approved by an admin yet.',
                        showConfirmButton: true,
                        confirmButtonText: 'OK'
                    });
                    $('#signinBtn').html("SIGN IN").prop("disabled", false);
                } else if (response === "unauthorized") {
                    Swal.fire({
                        icon: 'error',
                        title: 'Unauthorized',
                        text: 'Gmail not registered. Please register the Gmail first.',
                        showConfirmButton: true,
                        confirmButtonText: 'OK'
                    });
                    $('#signinBtn').html("SIGN IN").prop("disabled", false);
                }
            },
            error: function (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.',
                    showConfirmButton: true,
                    confirmButtonText: 'OK'
                });
                $('#signinBtn').html("SIGN IN").prop("disabled", false);
            }
        });
    }
};
</script>
