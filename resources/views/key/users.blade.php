@extends('layouts/contentNavbarLayout')

@section('title', 'Users')

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
<div class="container mt-3">
    <!-- Display Flash Message Using SweetAlert -->
    @if (session('message'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                title: '{{ session('color') === 'success' ? 'Success!' : 'Error!' }}',
                text: '{{ session('message') }}',
                icon: '{{ session('color') === 'success' ? 'success' : 'error' }}',
                confirmButtonText: 'OK',
                customClass: {
                    popup: 'swal2-popup-custom-zindex',
                    confirmButton: 'bg-primary'
                }
            });
        });
    </script>
    @endif

    <h3>Account List</h3>
    
    <!-- Table to display existing users -->
    <div class="table-responsive text-nowrap">
        <table class="table mt-3 table-sm">
            <thead> 
                <tr>
                    <th>No.</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @php
                $increment = 1;
                @endphp
                @foreach($users as $user)
                <tr>
                    <td class="text-nowrap">{{ $increment++ }}</td> 
                    <td class="text-nowrap">{{ $user->name }}</td>
                    <td class="text-nowrap">{{ $user->email }}</td>
                    <td class="text-nowrap">{{ ucfirst($user->user_role) }}</td>
                    <td class="text-nowrap">
                        @if($user->is_approved)
                            <span class="text-nowrap">Approved</span>
                        @else
                            <span class="text-nowrap">Pending</span>
                        @endif
                    </td>
                    <td class="text-nowrap">
                        @php
                            $encrypted = Crypt::encryptString($user->id);
                        @endphp

                        @if(!$user->is_approved)
                        <!-- Approve Button -->
                        <button type="button" class="btn btn-success btn-approve btn-sm" data-id="{{ $encrypted }}">
                            <i class="bx bx-check-circle"></i>
                        </button>
                        @endif

                        <!-- Update Role Button -->
                        <button type="button" class="btn btn-info btn-update btn-sm" data-id="{{ $encrypted }}" data-name="{{ $user->name }}" data-role="{{ $user->user_role }}">
                            <i class="bx bx-edit"></i>
                        </button>

                        <!-- Delete Button -->
                        <button type="button" class="btn btn-danger btn-delete btn-sm" data-id="{{ $encrypted }}">
                            @if($user->is_approved)
                            <i class="bx bx-trash"></i>
                            @else
                            <i class="bx bx-x-circle"></i>
                            @endif
                        </button>

                        <form id="approve-form-{{ $encrypted }}" action="{{ route('user.approve') }}" method="POST" style="display: none;">
                            @csrf
                            <input type="hidden" name="id" value="{{ $encrypted }}">
                        </form>
                        <form id="delete-form-{{ $encrypted }}" action="{{ route('user.delete') }}" method="POST" style="display: none;">
                            @csrf
                            <input type="hidden" name="id" value="{{ $encrypted }}">
                        </form>
                        <form id="update-role-form-{{ $encrypted }}" action="{{ route('user.updateRole') }}" method="POST" style="display: none;">
                            @csrf
                            <input type="hidden" name="id" value="{{ $encrypted }}">
                            <input type="hidden" name="role" class="role-input">
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Update Role Modal -->
<div class="modal fade" id="updateRoleModal" tabindex="-1" aria-labelledby="updateRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateRoleModalLabel">Update Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="modal-update-role-form">
                    <div class="mb-3">
                        <label for="modalUserName" class="form-label">User</label>
                        <input type="text" class="form-control" id="modalUserName" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="modalUserRole" class="form-label">Role</label>
                        <select class="form-select" id="modalUserRole">
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" id="saveRoleBtn">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const approveButtons = document.querySelectorAll('.btn-approve');
        const deleteButtons = document.querySelectorAll('.btn-delete');

        approveButtons.forEach(button => {
            button.addEventListener('click', function () {
                const accountId = this.getAttribute('data-id');
                Swal.fire({
                    title: "Approve Account?",
                    text: "This will allow the user to log in.",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonColor: "#28a745",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Yes",
                    cancelButtonText: "Cancel",
                    reverseButtons: true,
                    customClass: {
                        popup: 'swal2-popup-custom-zindex',
                        confirmButton: 'bg-success',
                        cancelButton: 'bg-secondary'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('approve-form-' + accountId).submit();
                    }
                });
            });
        });
        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                const accountId = this.getAttribute('data-id');
                const iconType = this.querySelector('i').classList.contains('bx-trash') ? 'delete' : 'reject';

                let title = iconType === 'delete' ? "Delete Account?" : "Reject Account?";
                let text = iconType === 'delete'
                    ? "This will delete the account."
                    : "This will reject the account.";

                Swal.fire({
                    title: title,
                    text: text,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Yes",
                    cancelButtonText: "Cancel",
                    reverseButtons: true,
                    customClass: {
                        popup: 'swal2-popup-custom-zindex',
                        confirmButton: 'bg-danger',
                        cancelButton: 'bg-secondary'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + accountId).submit();
                    }
                });
            });
        });
    });
    document.addEventListener("DOMContentLoaded", function () {
        const updateButtons = document.querySelectorAll('.btn-update');
        const saveRoleBtn = document.getElementById('saveRoleBtn');
        const updateRoleModal = new bootstrap.Modal(document.getElementById('updateRoleModal'));
        let selectedUserId;

        updateButtons.forEach(button => {
            button.addEventListener('click', function () {
                selectedUserId = this.getAttribute('data-id');
                document.getElementById('modalUserName').value = this.getAttribute('data-name');
                document.getElementById('modalUserRole').value = this.getAttribute('data-role');
                updateRoleModal.show();
            });
        });

        saveRoleBtn.addEventListener('click', function () {
            const selectedRole = document.getElementById('modalUserRole').value;
            const form = document.getElementById('update-role-form-' + selectedUserId);
            form.querySelector('.role-input').value = selectedRole;
            form.submit();
        });
    });
</script>
