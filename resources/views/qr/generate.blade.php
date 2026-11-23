@extends('layouts.app')

@section('content')
    <div class="qr-code-container">
        <h3>Scan the QR code to return your key</h3>
        {!! QrCode::size(200)->generate(route('qr.access', ['token' => $user->qr_token])) !!}
    </div>
@endsection
