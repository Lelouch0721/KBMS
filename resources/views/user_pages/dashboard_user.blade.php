@extends('layouts.contentNavbarLayout')

@section('title', 'Dashboard')

@section('vendor-style')
<style>
    .container {
        margin-top: 20px;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .swal2-popup-custom-zindex {
        z-index: 9999 !important;
    }
    .search-form {
        width: 100%;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    .menu-container {
        margin-top: 0;
        align-self: flex-start;
    }
    .select-menu {
        position: relative;
        background-color: #FFFFFF;
        padding: 10px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 150px;
    }
    .select-menu button {
        background-color: #007BFF;
        color: #FFFFFF;
        border: none;
        padding: 8px 12px;
        border-radius: 5px;
        cursor: pointer;
        width: 100%;
        text-align: left;
        font-size: 0.875rem;
    }
    .select-menu-content {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        background-color: #FFFFFF;
        border: 1px solid #ddd;
        border-radius: 5px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 100%;
        z-index: 1000;
    }
    .select-menu-content a {
        display: block;
        padding: 8px 12px;
        color: #696969;
        text-decoration: none;
        font-size: 0.875rem;
    }
    .select-menu-content a:hover {
        background-color: #f0f0f0;
    }
    .key-section {
        background-color: #FFFFFF;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        width: 100%;
    }
    .key-section h4 {
        font-size: 1.5rem;
        margin-bottom: 1rem;
        color: default;
    }
    .key-list {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .key-item {
        position: relative; /* Establish a relative position for the container */
        border: 1px solid #ddd;
        padding: 0.5rem; /* Adjust padding */
        border-radius: 0.25rem;
        background-color: #f9f9f9;
        width: calc(25% - 1rem); /* Adjust width as needed */
        box-sizing: border-box;
    }
    .key-item h5 {
        margin: 0;
        font-size: 1rem;
    }
    .key-item p {
        margin: 0;
    }
    .borrowed-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: #28a745;
        color: #FFFFFF;
        padding: 0.2rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.8rem;
        font-weight: bold;
    }
    .btn-container {
        display: flex;
        gap: 0.5rem;
    }
    .btn-warning {
        background-color: #FFC107;
        border-color: #FFC107;
    }
    .btn-warning:hover {
        background-color: #e0a800;
        border-color: #d39e00;
    }
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }
    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #004085;
    }
    .title-container {
        text-align: center;
        margin-bottom: 20px;
        width: 100%;
    }
    .title-container h1 {
        font-size: 3rem;
        color: #000000;
        margin: 0;
    }

    /* Responsive styles for mobile devices */
@media (max-width: 768px) {
    .btn-primary, .btn-warning {
        padding: 10px 15px;
        font-size: 1.0rem;
        border-radius: 10px;
    }
    .btn-container {
        gap: 0.25rem;
        justify-content:center; /* Center-align buttons */
        display: flex;
    }
    .key-item {
        width: 100%; /* 80% width on smaller screens */
        margin: 0 auto 1rem; /* Center horizontally and add spacing */
    }
}
</style>
@endsection

@if (session('message'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            title: '{{ session("color") === "success" ? "Success!" : "Error!" }}',
            text: '{{ session("message") }}',
            icon: '{{ session("color") === "success" ? "success" : "error" }}',
            confirmButtonText: 'OK'
        });
    });
</script>
@endif

@section('content')
<div class="search-form">
    <form action="{{ route('keys.search') }}" method="GET" class="form-inline">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ $searchTerm }}">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </form>
</div>
<div class="menu-container">
    <div class="select-menu">
        <button id="menu-button">View</button>
        <div class="select-menu-content" id="menu-content">
            <a href="{{ route('recent.borrowed.keys') }}">Recently Borrowed Keys</a>
            <a href="{{ route('recent.borrowed.remotes') }}">Recently Borrowed Remote</a>
            <a href="{{ route('user.returned') }}">Returned Items</a>
        </div>
    </div>
</div>
<div class="key-section">
    @php
        // Group the products by type and sort the types
        $groupedProducts = $products->groupBy('type')->sortKeys();
    @endphp
    @foreach($groupedProducts as $type => $items)
    <div class="type-section">
        <h4>{{ $type }}s</h4>
        <div class="key-list">
            @foreach($items->sortBy(function ($product) {
                // Custom sorting: first sort alphabetically, then by number
                preg_match('/(\D+)(\d+)/', $product->name, $matches); 
                $alphaPart = $matches[1] ?? ''; // Non-numeric part (e.g., "ICT")
                $numPart = isset($matches[2]) ? (int)$matches[2] : 0; // Numeric part (e.g., 1, 10)
                return [$alphaPart, $numPart];
            }) as $product)
            @php
                $encrypted = Crypt::encryptString($product->id);
                $borrowedKey = $borrowedKeys->firstWhere('item_id', $product->id);
                $isBorrowed = $borrowedKey !== null;
            @endphp
            <div class="key-item">
                <h5>{{ $product->name }}</h5>
                <p class="item-type">{{ $product->type }}</p> <!-- Display item type here -->
                @if($isBorrowed)
                    <div class="borrowed-badge">Borrowed by: {{ $borrowedKey->borrower_name }}</div>
                @endif
                <div class="btn-container">
                    @if ($isBorrowed)
                        @if ($borrowedKey->borrower_name === auth()->user()->name)
                            <button 
                                type="button" 
                                class="btn btn-warning mb-4 borrow-button"
                                data-id="{{ $encrypted }}" 
                                data-type="{{ strtolower($product->type) }}">
                                Return
                            </button>
                            <form 
                                id="action-form-{{ $encrypted }}" 
                                action="{{ route('borrow.return') }}" 
                                method="POST" 
                                style="display: none;">
                                @csrf
                                <input type="hidden" name="id" value="{{ $encrypted }}">
                            </form>
                        @else
                            <button 
                                type="button" 
                                class="btn btn-secondary mb-4" 
                                disabled>
                                Borrowed
                            </button>
                        @endif
                    @else
                        <button 
                            type="button" 
                            class="btn btn-primary mb-4 borrow-button"
                            data-id="{{ $encrypted }}" 
                            data-type="{{ strtolower($product->type) }}">
                            Borrow
                        </button>
                        <form 
                            id="action-form-{{ $encrypted }}" 
                            action="{{ route('borrow.borrow') }}" 
                            method="POST" 
                            style="display: none;">
                            @csrf
                            <input type="hidden" name="id" value="{{ $encrypted }}">
                        </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endsection
@section('vendor-script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const actionButtons = document.querySelectorAll('.borrow-button');
        const menuButton = document.getElementById('menu-button');
    const menuContent = document.getElementById('menu-content');

    // Toggle menu visibility
    menuButton.addEventListener('click', function () {
        console.log("[Menu] Toggle clicked.");
        menuContent.style.display = menuContent.style.display === 'block' ? 'none' : 'block';
    });

    // Close menu when clicking outside
    document.addEventListener('click', function (event) {
        const isClickInsideMenu = menuButton.contains(event.target) || menuContent.contains(event.target);
        if (!isClickInsideMenu) {
            menuContent.style.display = 'none';
        }
    });

        actionButtons.forEach(button => {
            button.addEventListener('click', function () {
                const keyId = this.getAttribute('data-id');
                const actionType = this.textContent.trim().toLowerCase();
                const itemType = this.getAttribute('data-type');
                const endpoint = actionType === 'borrow' ? '{{ route("borrow.borrow") }}' : '{{ route("borrow.return") }}';

                Swal.fire({
                    title: `${actionType.charAt(0).toUpperCase() + actionType.slice(1)} ${itemType}?`,
                    text: `Are you sure you want to ${actionType} this ${itemType}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No',
                    reverseButtons: true
                }).then((confirmResult) => {
                    if (confirmResult.isConfirmed) {
                        Swal.fire({
                            title: "Connecting to device...",
                            text: "Please wait...",
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        fetch(endpoint, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({ id: keyId })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.title === "Success") {
                                Swal.fire({
                                    title: "Success!",
                                    text: data.message,
                                    icon: "success",
                                    confirmButtonText: "OK"
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                throw new Error(data.message || "An unexpected error occurred.");
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: "Error!",
                                text: error.message || "Something went wrong. Please try again.",
                                icon: "error",
                                confirmButtonText: "OK"
                            });
                        });
                    }
                });
            });
        });
    });
</script>
@endsection
