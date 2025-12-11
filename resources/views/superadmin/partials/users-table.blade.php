            <div class="table-responsive">
                <table class="table users-table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">User</th>
                            <th>Email</th>
                            <th class="text-center">Role</th>
                            <th>Registered</th>
                            <th class="text-center pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                        @foreach($users as $user)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3 text-white"
                                            style="width: 40px; height: 40px; font-size: 0.9rem; font-weight: 600;">
                                            {{ substr($user->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $user->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span>{{ $user->email }}</span>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge rounded-pill 
                                                                                                                                @if($user->role == 'superadmin') bg-danger
                                                                                                                                @elseif($user->role == 'admin') bg-warning
                                                                                                                                @else bg-primary
                                                                                                                                @endif"
                                        style="font-size: 0.75rem; padding: 0.5rem 1rem;">
                                        {{ ucfirst($user->role === 'user' ? 'patient' : $user->role) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="small">{{ $user->created_at->format('M d, Y') }}</span>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="btn-group" role="group">
                                        <button
                                            class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center"
                                            data-bs-toggle="modal" data-bs-target="#viewUserModal{{ $user->id }}" title="View User">
                                            <i class="fas fa-eye text-info"></i>
                                        </button>
                                        <button
                                            class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center"
                                            data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}" title="Edit User">
                                            <i class="fas fa-edit text-warning"></i>
                                        </button>
                                        @if($user->id !== Auth::id())
                                            <button
                                                class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center"
                                                data-bs-toggle="modal" data-bs-target="#archiveUserModal{{ $user->id }}"
                                                title="Archive User">
                                                <i class="fas fa-archive text-danger"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
