@extends('layouts/contentNavbarLayout')

@section('title', 'Borrowed Item')
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

  <h3>Borrower Item List</h3>
  <div class="table-responsive text-nowrap">
  
  <table class="table mt-3 table-sm">
    <thead>
      <tr>
        <th class=" text-nowrap">No.</th>
        <th class=" text-nowrap">Borrower</th>
        <th class=" text-nowrap">Item</th>
        <th class=" text-nowrap">Status</th>
        <th class=" text-nowrap">Borrowed Date</th>
        <th class=" text-nowrap">Actions</th>
      </tr>
    </thead>
    <tbody>
      @php
      $increment = 1;
      @endphp
      @foreach($borrows as $borrows)
        <tr>
          <td class="text-capitalize text-nowrap">{{ $increment++ }}</td>   
          <td class="text-capitalize text-nowrap">{{ $borrows->borrower }}</td>
          <td class="text-capitalize text-nowrap">{{ $borrows->key }}</td>  
          <td class="text-capitalize text-nowrap">{{ $borrows->status }}</td>
          <td class="text-nowrap">{{ $borrows->date_borrowed }}</td>
          <td class="d-flex">
            <!-- Edit button -->
            @php
              $encrypted = Crypt::encryptString($borrows->id);
            @endphp
            <button class="btn btn-info me-2 btn-sm" data-bs-toggle="modal" data-bs-target="#editBorrowModal-{{ $encrypted }}">
              <i class="bx bx-edit"></i>
            </button>
          </td>
        </tr>


        <div class="modal fade" id="editBorrowModal-{{ $encrypted }}" tabindex="-1" aria-labelledby="editKeyModalLabel-{{ $encrypted }}" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="editKeyModalLabel-{{ $encrypted }}">Update Item Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form id="updateBorrow" action="{{ route('borrow.update') }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  <div class="mb-3">
                    <label for="id" class="form-label">Information</label>
                    <div class="bg-dark px-3 py-4 rounded text-white">
                      <h6 class="text-white mb-1">Item: <span class="text-info">{{ $borrows->key }}</span>  </h6>
                      <h6 class="text-white mb-0">Borrowed Date:  <span class="text-info">{{ $borrows->date_borrowed }}</span></h6>
                    </div>
                    <input type="hidden" name="id" class="form-control" value="{{ $encrypted }}" required>
                  </div>
                  <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-control form-control-select" name="status" id="status" required>
                      <option value="">Borrowed</option>
                      <option value="returned" {{ $borrows->status == 'returned' ? 'selected' : '' }}>Returned</option>
                    </select>
                  </div>
                  <div class="d-flex justify-content-End">
                    <button type="submit" class="btn btn-info">Save</button>
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
</div>
@endsection
