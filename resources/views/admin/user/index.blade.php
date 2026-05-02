@extends('layouts.app')

@section('content')
    <div class="table-section fade-in-up">

        @if(session('success'))
            <div class="alert-auto-close"
                style="background: var(--success); color: white; padding: 10px 15px; border-radius: 8px; margin-bottom: 15px;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert-auto-close"
                style="background: var(--danger); color: white; padding: 10px 15px; border-radius: 8px; margin-bottom: 15px;">
                <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
            </div>
        @endif

        <div class="table-header">
            <h2 style="color: var(--primary-hover);">Kelola User</h2>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Telusuri username..."
                    style="padding: 8px; border: 1px solid var(--primary); border-radius: 5px; min-width: 250px;">
                <button onclick="showAddUserModal()" class="btn-primary">
                    <i class="fas fa-plus"></i> Tambah User
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table id="userTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>No. Telepon</th>
                        <th>Role</th>
                        <th>Tanggal Lahir</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>USR-{{ str_pad($user->id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td style="font-weight: bold;">{{ $user->username }}</td>
                            <td>{{ $user->no_hp }}</td>
                            <td>
                                @if($user->role == 'admin')
                                    <span 
                                        style="background: var(--primary-hover); color: white; padding: 5px 10px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                        ADMIN
                                    </span>
                                @else
                                    <span 
                                        style="background: var(--primary-hover); color: white; padding: 5px 10px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                        STAFF
                                    </span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($user->tanggal_lahir)->format('d M Y') }}</td>
                            <td style="min-width: 100px;">
                                <div style="display: flex; gap: 15px; justify-content: center; align-items: center;">

                                    <button
                                        title="Edit User"
                                        onclick="showEditUserModal(
                                            '{{ $user->id }}',
                                            '{{ addslashes(htmlspecialchars($user->username, ENT_QUOTES)) }}',
                                            '{{ $user->no_hp }}',
                                            '{{ $user->role }}',
                                            '{{ \Carbon\Carbon::parse($user->tanggal_lahir)->format('Y-m-d') }}'
                                        )"
                                        style="border:none; background:none; color:var(--primary); cursor:pointer; font-size:16px;">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST"
                                        style="margin: 0; padding: 0;"
                                        onsubmit="return confirm('Yakin ingin menghapus pengguna ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            style="border:none; background:none; color:var(--danger); cursor:pointer; font-size:16px;"
                                            title="Hapus User">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL TAMBAH USER --}}
    <div class="modal-overlay" id="addUserModal">
        <div class="modal-box" style="max-width: 500px;">
            <h3>Tambah User</h3>

            <form action="{{ route('admin.user.store') }}" method="POST" style="text-align: left; margin-top: 20px;">
                @csrf

                <div style="margin-bottom: 15px;">
                    <label style="font-weight: bold; display:block; margin-bottom:5px;">Username</label>
                    <input type="text" name="username" required
                        style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="font-weight: bold; display:block; margin-bottom:5px;">No. Telepon</label>
                    <input type="text" name="no_hp" required
                        style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
                </div>

                <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                    <div style="flex:1;">
                        <label style="font-weight: bold; display:block; margin-bottom:5px;">Role</label>
                        <select name="role" required
                            style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
                            <option value="admin">Admin</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>

                    <div style="flex:1;">
                        <label style="font-weight: bold; display:block; margin-bottom:5px;">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" required
                            style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
                    </div>
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="font-weight: bold; display:block; margin-bottom:5px;">Password</label>
                    <input type="password" name="password" required
                        style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="hideAddUserModal()">Batal</button>
                    <button type="submit" class="btn-confirm">Simpan Pengguna</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT USER --}}
    <div class="modal-overlay" id="editUserModal">
        <div class="modal-box" style="max-width: 500px;">
            <h3>Edit User</h3>

            <form id="editUserForm" action="#" method="POST" style="text-align: left; margin-top: 20px;">
                @csrf
                @method('PUT')

                <div style="margin-bottom: 15px;">
                    <label style="font-weight: bold; display:block; margin-bottom:5px;">Username</label>
                    <input type="text" name="username" id="edit_username" required
                        style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="font-weight: bold; display:block; margin-bottom:5px;">No. Telepon</label>
                    <input type="text" name="no_hp" id="edit_no_hp" required
                        style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
                </div>

                <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                    <div style="flex:1;">
                        <label style="font-weight: bold; display:block; margin-bottom:5px;">Role</label>
                        <select name="role" id="edit_role" required
                            style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
                            <option value="admin">Admin</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>

                    <div style="flex:1;">
                        <label style="font-weight: bold; display:block; margin-bottom:5px;">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" id="edit_tanggal_lahir"
                            style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="hideEditUserModal()">Batal</button>
                    <button type="submit" class="btn-confirm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection