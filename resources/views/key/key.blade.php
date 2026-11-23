@extends('layouts/contentNavbarLayout')

@section('title', 'Classroom')

<style>
      /* General Layout */
      .container {
        margin-top: 20px;
        padding: 15px;
        background-color: #f8f9fa; /* Light background */
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
</style>
@section('content')
<div class="container mt-4">
   <!-- Display Flash Message Using SweetAlert -->
   @if (session('message'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                title: '{{ session("color") === "success" ? "Success!" : "Error!" }}',
                text: @json(session('message')),
                icon: '{{ session("color") === "success" ? "success" : "error" }}',
                confirmButtonText: 'OK'
            });
        });
    </script>
@endif

  <h3>Item List</h3>  
  
  <!-- Button to create a new key -->
  <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#createkeyModal">
  <i class="bx bx-plus-circle me-2"></i>
  Add Item
  </button>
  <div class="table-responsive text-nowrap">
  
  <!-- Table to display existing key -->
  <table class="table mt-3 table-sm">
    <thead>
      <tr>
        <th>Name</th>
        <th>Type</th>
        <th>Actions</th> <!-- Actions column for edit and delete -->
      </tr>
    </thead>
    <tbody>
      @foreach($keys as $key) <!-- Loop through each key -->
        <tr>
          <td class="text-nowrap">{{ $key->name }}</td>
          <td class="text-nowrap">{{ $key->type }}</td>
          <td class="text-nowrap"> <!-- "Actions" column -->
            <!-- Edit button to open edit modal -->
            @php
              $encrypted = Crypt::encryptString($key->id);
            @endphp
            <button class="btn btn-info me-2 btn-sm" data-bs-toggle="modal" data-bs-target="#editkeyModal-{{ $encrypted }}">
              <i class="bx bx-edit"></i>
            </button>

            <!-- Delete button -->
            <button type="button" class="btn btn-danger btn-delete btn-sm" data-id="{{ $encrypted }}">
              <i class="bx bx-trash"></i>
            </button>
            <form id="delete-form-{{ $encrypted }}" action="{{ route('key.delete') }}" method="POST" style="display: none;">
                @csrf
                @method('post')
                <input type="hidden" name="id" value="{{ $encrypted }}">
            </form>

          </td>
        </tr>

        <!-- Modal for editing this specific key -->
        <div class="modal fade" id="editkeyModal-{{ $encrypted }}" tabindex="-1" aria-labelledby="editkeyModalLabel-{{ $encrypted }}" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="editkeyModalLabel-{{ $encrypted }}">Edit Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form id="updateKey" action="{{ route('key.update') }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="hidden" name="id" class="form-control" value="{{ $encrypted }}">
                    <input type="text" name="name" class="form-control" required value="{{ $key->name }}">
                  </div>
                  <div class="mb-3">
                    <label for="type" class="form-label">Type</label>
                    <select name="type" class="form-control" required>
                      <option value="Key" {{ $key->type === 'Key' ? 'selected' : '' }}>Key</option>
                      <option value="Remote" {{ $key->type === 'Remote' ? 'selected' : '' }}>Remote</option>
                    </select>
                  </div>
                  <!-- Modified Update button with Bootstrap variant -->
                  <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-info">Update</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </tbody>
  </table>
  </div>

  <!-- Modal to create a new key -->
  <div class="modal fade" id="createkeyModal" tabindex="-1" aria-labelledby="createkeyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createkeyModalLabel">Add Item</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="{{ route('key.add') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
              <label for="name" class="form-label">Name</label>
              <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="type" class="form-label">Type</label>
              <select name="type" class="form-control" required>
                <option value="Key">Key</option>
                <option value="Remote">Remote</option>
              </select>
            </div>
            <div class="d-flex justify-content-end">
              <button type="submit" class="btn btn-info">Save</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('vendor-script')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const keyId = this.getAttribute('data-id');
            Swal.fire({
                title: "Delete Item?",
                text: "This will delete the Item!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: '#6c757d',
                confirmButtonText: "Yes",
                cancelButtonText: "Cancel",
                reverseButtons: true // Reverses the button order
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + keyId).submit();
                }
            });
        });
    });
});
</script>
@endsection
