@extends('layouts/contentNavbarLayout')

@section('title', 'Return History')

<style>
    /* General Layout */
    .container {
        margin-top: 20px;
        padding: 15px;
        background-color: #f8f9fa; /* Light background */
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .table-responsive {
        margin-top: 15px;
    }

    /* Title */
    h3 {
        color: #343a40;
        font-weight: 600;
    }

    /* Search Bar */
    .search-bar {
        width: 100%;
        padding: 8px;
        margin-bottom: 15px;
        font-size: 14px;
        border-radius: 4px;
        border: 1px solid #ced4da;
    }

    .short-date-input {
        max-width: 120px; 
        text-align: center;
    }

    .text-center {
        color: #6c757d;
    }

    /* Action Buttons */
    .action-button {
        padding: 8px 12px;
        font-size: 13px;
        border-radius: 4px;
        color: #ffffff;
        cursor: pointer;
        border: none;
        transition: background-color 0.3s;
    }

    #clear-filters {
        background-color: #6c757d;
    }

    .delete-all {
        background-color: #dc3545;
        margin-left: 10px;
    }

    .action-button:hover {
        opacity: 0.9;
    }

    /* Hidden Rows */
    .hidden {
        display: none;
    }
</style>

@section('content')
<div class="container mt-4">
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

    <h3>Returned List</h3>

    <!-- Search Bar -->
    <input type="text" class="form-control search-bar" id="search-input" placeholder="Search borrower or item">

    <div class="d-flex align-items-center justify-content-start mb-3 gap-2">
        <!-- Calendar Button with Input -->
        <div class="input-group" style="max-width: 300px;">
            <button class="btn btn-outline-secondary" id="date-picker-btn">
                <i class="fa fa-calendar"></i>
            </button>
            <input type="text" id="date-picker" class="form-control short-date-input" placeholder="Select a date" readonly>
        </div>

        <!-- Show All Button -->
        <button class="action-button" id="clear-filters">Show All</button>
    </div>

    <div class="table-responsive text-nowrap">
        <table class="table mt-3 table-sm">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Borrower</th>
                    <th>Item</th>
                    <th>Status</th>
                    <th>Returned Date</th>
                </tr>
            </thead>
            <tbody id="borrow-table-body">
                @forelse($borrows as $borrow)
                    <tr class="borrow-row hidden" data-date="{{ \Carbon\Carbon::parse($borrow->date_served)->format('Y-m-d') }}">
                        <td class="row-number"></td>
                        <td class="text-capitalize">{{ $borrow->borrower }}</td>
                        <td class="text-capitalize">{{ $borrow->key }}</td>
                        <td class="text-capitalize">{{ $borrow->status }}</td>
                        <td>{{ $borrow->date_served }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No returned items found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<script>
document.addEventListener("DOMContentLoaded", function () {
    const rows = document.querySelectorAll('.borrow-row');
    const searchInput = document.getElementById('search-input');
    const clearButton = document.getElementById('clear-filters');
    const datePickerInput = document.getElementById('date-picker');
    const datePickerBtn = document.getElementById('date-picker-btn');

    // Initialize Flatpickr
    const flatpickrInstance = flatpickr(datePickerInput, {
        enableTime: false,
        dateFormat: "Y-m-d", // Ensure this matches the format stored in 'data-date'
        onChange: function (selectedDates, dateStr) {
            filterRows(dateStr); // Trigger filtering on date selection
        },
    });

    // Open date picker on button click
    datePickerBtn.addEventListener('click', () => flatpickrInstance.open());

    /**
     * Filters rows based on search query and selected date.
     * @param {string|null} selectedDate - The selected date to filter by.
     */
    function filterRows(selectedDate = null) {
        const searchQuery = searchInput.value.trim().toLowerCase(); // Normalize and trim search query
        let visibleIndex = 0; // Counter for visible rows

        rows.forEach(row => {
            const rowDate = row.getAttribute('data-date'); // Get row's date attribute
            const borrowerColumnText = row.cells[1].innerText.toLowerCase(); // Borrower column (2nd column)
            const itemColumnText = row.cells[2].innerText.toLowerCase(); // Item column (3rd column)
            const statusColumnText = row.cells[3].innerText.toLowerCase(); // Status column (4th column)

            // Match search term across borrower, item, or status columns
            const regex = new RegExp(`\\b${searchQuery}\\b`, 'i');
            const matchesSearch = searchQuery 
                ? regex.test(borrowerColumnText) || regex.test(itemColumnText) || regex.test(statusColumnText)
                : true; // Match across all columns (borrower, item, status)

            // If no date selected, show all rows; otherwise, filter by date
            const matchesDate = !selectedDate || rowDate === selectedDate; 

            if (matchesDate && matchesSearch) {
                row.classList.remove('hidden'); // Show row
                row.querySelector('.row-number').textContent = ++visibleIndex; // Update row number
            } else {
                row.classList.add('hidden'); // Hide row
            }
        });
    }

    // Filter rows on search input
    searchInput.addEventListener('input', () => filterRows());

    // Clear filters and reset table
    clearButton.addEventListener('click', () => {
        searchInput.value = ''; // Clear search input
        flatpickrInstance.clear(); // Clear selected date
        filterRows(); // Reset filtering
    });
});
</script>  
@endsection
