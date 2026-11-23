@extends('layouts.contentNavbarLayout')

@section('title', 'Recently Borrowed Keys')

@section('vendor-style')
<style>
    .recent-borrows {
        background-color: #FFFFFF;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .recent-borrows h5 {
        font-size: 1.5rem;
        margin-bottom: 1rem;
        color: default;
    }

    .table-container {
        overflow-x: auto; /* Ensures table is scrollable on small screens */
        border-radius: 0.25rem;
        background-color: #f9f9f9;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0; /* Removes margin to fit neatly within box */
    }

    .table th, .table td {
        padding: 12px;
        border: 1px solid #ddd;
        text-align: left;
        color: #000000; /* Set text color to black */
    }

    .table th {
        background-color: #f4f4f4;
        color: #007BFF; /* Keep header text color */
    }

    .table td {
        background-color: #ffffff;
    }

    .btn-back {
        background-color: #6c757d; /* Gray color */
        border-color: #6c757d;
        color: #FFFFFF;
        padding: 0.5rem 1rem;
        border-radius: 0.25rem;
        font-size: 1rem;
        text-decoration: none;
        display: inline-block;
        margin-bottom: 20px;
    }

    .btn-back:hover {
        background-color: #5a6268; /* Darker gray on hover */
        border-color: #545b62;
    }
</style>
@endsection

@section('content')
<div class="container mt-4">
    <div class="recent-borrows">
        <h5>Recently Borrowed Keys</h5>
        @if($recentBorrowedKeysForAll->isNotEmpty())
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Borrower Name</th>
                            <th>Borrowed Key</th>
                            <th>Borrowed Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentBorrowedKeysForAll as $key)
                            <tr>
                                <td>{{ $key->borrower_name }}</td>
                                <td>{{ $key->key_name }}</td>
                                <td>{{ $key->date_borrowed }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p>No recent borrowed keys.</p>
        @endif
    </div>
</div>
@endsection
