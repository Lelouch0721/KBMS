@extends('layouts.app')

@section('content')
    <div class="return-container">
        <h3>Welcome, {{ $user->name }}</h3>
        <form action="{{ route('qr.return', ['token' => $user->qr_token]) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary">Return Key</button>
        </form>
    </div>
@endsection
