<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2>Verify OTP</h2>
    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif
    @if (session('errors'))
        <div class="alert alert-danger">
            @foreach (session('errors') as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif
    <form action="{{ route('verify-otp') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="otp">OTP:</label>
            <input type="text" class="form-control" id="otp" name="otp" required>
            @if ($errors->has('otp'))
                <span class="text-danger">{{ $errors->first('otp') }}</span>
            @endif
        </div>
        <button type="submit" class="btn btn-primary">Verify OTP</button>
    </form>
</div>
</body>
</html>
