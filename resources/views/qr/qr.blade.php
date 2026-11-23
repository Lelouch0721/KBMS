@extends('layouts.contentNavbarLayout')

@section('title', 'QR Code')

@section('vendor-style')
<style>
    .qr-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 80vh;
        text-align: center;
        padding: 20px;
        box-sizing: border-box;
    }

    .qr-container h2 {
        font-size: 1.75rem;
        margin-bottom: 1.5rem;
    }

    .qr-code-wrapper {
        display: flex;
        justify-content: center;
        gap: 20px;
        width: 100%;
        max-width: 800px;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
    }

    .qr-code {
        text-align: center;
        margin: 10px;
        flex: 1 1 200px;
        max-width: 200px;
        display: flex;
        flex-direction: column;
        align-items: center;  /* Center the QR code image and the text */
        justify-content: center;
    }

    .qr-code h3 {
        margin-bottom: 10px; /* Add some space between the text and the QR code */
    }

    .qr-code img {
        width: 100%;
        max-width: 200px;
        height: auto;
        margin-bottom: 1rem;
    }

    .qr-container .return-button {
        background-color: #007BFF;
        color: white;
        border: none;
        padding: 10px 20px;
        margin: 5px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1rem;
        transition: background-color 0.3s;
    }

    .qr-container .return-button:hover {
        background-color: #0056b3;
    }

    /* Responsive styles for smaller screens */
    @media (max-width: 768px) {
        .qr-code-wrapper {
            flex-direction: column;
            align-items: center;
            justify-content: center; /* Ensure it's centered */
        }

        .qr-code {
            margin: 15px 0;
            flex: 1 1 150px;
            max-width: 150px;
        }

        .qr-code img {
            max-width: 150px;
        }

        .qr-container h2 {
            font-size: 1.5rem;
        }

        .qr-container .return-button {
            font-size: 0.9rem;
            padding: 8px 16px;
        }
    }

    @media (max-width: 480px) {
        .qr-code-wrapper {
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .qr-code {
            margin: 10px 0;
            flex: 1 1 120px; /* Adjust size for very small screens */
            max-width: 120px;
        }

        .qr-code img {
            max-width: 120px;
        }

        .qr-container h2 {
            font-size: 1.25rem;
        }

        .qr-container .return-button {
            font-size: 0.8rem;
            padding: 6px 12px;
        }
    }
</style>
@endsection

@section('content')
<div class="qr-container">
    <h4>Return Items via QR Code</h4>
    <div class="qr-code-wrapper">
        <div class="qr-code">
            <h3>Return Key</h3>
            {!! $qrCodeKey !!}
        </div>
        <div class="qr-code">
            <h3>Return Both</h3>
            {!! $qrCodeBoth !!}
        </div>
        <div class="qr-code">
            <h3>Return Remote</h3>
            {!! $qrCodeRemote !!}
        </div>
    </div>
</div>
@endsection
