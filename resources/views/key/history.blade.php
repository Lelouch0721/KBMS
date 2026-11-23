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

        <!-- Delete by Month Button -->
        <button class="action-button delete-all" id="delete-by-month">Delete Returned for Selected Month</button>
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
    const deleteByMonthButton = document.getElementById('delete-by-month');
    const datePickerInput = document.getElementById('date-picker');
    const datePickerBtn = document.getElementById('date-picker-btn');
    
    const flatpickrInstance = flatpickr(datePickerInput, {
        enableTime: false,
        dateFormat: "Y-m-d",
        onChange: function (selectedDates, dateStr) {
            filterRows(dateStr); 
        },
    });

    datePickerBtn.addEventListener('click', () => flatpickrInstance.open());

    function filterRows(selectedDate = null) {
        const searchQuery = searchInput.value.toLowerCase(); 
        let visibleIndex = 0;

        rows.forEach(row => {
            const rowDate = row.getAttribute('data-date'); 
            const matchesDate = (!selectedDate || rowDate === selectedDate); 

            const matchesSearch = row.innerText.toLowerCase().includes(searchQuery);

            if (matchesDate && matchesSearch) {
                row.classList.remove('hidden');
                row.querySelector('.row-number').textContent = ++visibleIndex;
            } else {
                row.classList.add('hidden'); 
            }
        });
    }
    searchInput.addEventListener('input', () => filterRows());

    clearButton.addEventListener('click', () => {
        searchInput.value = ''; 
        flatpickrInstance.clear(); 
        filterRows(); 
    });

    deleteByMonthButton.addEventListener('click', () => {
        const selectedDate = flatpickrInstance.selectedDates[0];
        if (!selectedDate) {
            Swal.fire('No Date Selected', 'Please select a month to delete records.', 'warning');
            return;
        }

        // Extract the year and month in 'YYYY-MM' format
        const monthName = selectedDate.toLocaleString('default', { month: 'long' });
        const selectedMonth = selectedDate.getFullYear() + '-' + String(selectedDate.getMonth() + 1).padStart(2, '0');

        // Confirmation dialog for deletion
        Swal.fire({
            title: 'Delete Records?',
            text: `This will delete all records for ${monthName} ${selectedDate.getFullYear()}!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes",
            cancelButtonText: "Cancel",
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                // Send DELETE request to backend
                fetch("{{ route('borrows.deleteByMonth') }}", {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}', // CSRF token for Laravel security
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ month: selectedMonth }) // Send selected month
                })
                .then(response => response.json())  // Parse the JSON response from the backend
                .then(data => {
                    Swal.fire({
                        title: data.success ? 'Deleted!' : 'Error!',
                        text: data.message,
                        icon: data.success ? 'success' : 'error',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        if (data.success) {
                            window.location.reload();  // Reload the page if deletion is successful
                        }
                    });
                })
                .catch(error => {
                    Swal.fire('Error', 'Failed to delete records.', 'error');
                    console.error('Error:', error);
                });
            }
        });
    });
});
</script>
@endsection