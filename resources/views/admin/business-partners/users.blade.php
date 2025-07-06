{{-- File Path: resources/views/admin/business-partners/users.blade.php --}}

@extends('layouts.admin')

@section('title', 'Manage Users - ' . $businessPartner->name)

@section('page-header')
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Manage Users: {{ $businessPartner->name }}
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <a href="{{ route('admin.business-partners.index') }}" class="text-indigo-600 hover:text-indigo-500">Business Partners</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.business-partners.show', $businessPartner->id) }}" class="text-indigo-600 hover:text-indigo-500">{{ $businessPartner->name }}</a>
                <span class="mx-2">/</span>
                <span>Manage Users</span>
            </div>
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <svg class="mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                </svg>
                {{ $businessPartner->users->count() }} users associated
            </div>
        </div>
    </div>
    <div class="mt-4 flex space-x-3 md:ml-4 md:mt-0">
        <a href="{{ route('admin.business-partners.show', $businessPartner->id) }}" 
           class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 010-1.06L10.94 8.25H4a.75.75 0 010-1.5h6.94L7.72 3.53a.75.75 0 011.06-1.06l4.5 4.5a.75.75 0 010 1.06l-4.5 4.5a.75.75 0 01-1.06 0z" clip-rule="evenodd" />
            </svg>
            Back to Partner
        </a>
        <button onclick="showAddUserModal()" 
                class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
            </svg>
            Add User
        </button>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Users List -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Associated Users</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Users who have access to this business partner account
                    </p>
                </div>
            </div>
        </div>

        @if($businessPartner->users->count() > 0)
        <ul role="list" class="divide-y divide-gray-200">
            @foreach($businessPartner->users as $user)
            <li class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center min-w-0 flex-1">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-full {{ $user->pivot->is_primary_contact ? 'bg-indigo-100' : 'bg-gray-100' }} flex items-center justify-center">
                                <span class="text-lg font-medium {{ $user->pivot->is_primary_contact ? 'text-indigo-800' : 'text-gray-600' }}">
                                    {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                                </span>
                            </div>
                        </div>
                        <div class="ml-4 min-w-0 flex-1">
                            <div class="flex items-center">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $user->full_name }}
                                </p>
                                @if($user->pivot->is_primary_contact)
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    Primary Contact
                                </span>
                                @endif
                            </div>
                            <div class="flex items-center space-x-4 mt-1">
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                @if($user->phone)
                                <p class="text-sm text-gray-500">{{ $user->phone }}</p>
                                @endif
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                    {{ $user->pivot->access_level === 'admin' ? 'bg-red-100 text-red-800' : 
                                       ($user->pivot->access_level === 'user' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($user->pivot->access_level) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        @unless($user->pivot->is_primary_contact)
                        <button onclick="setPrimaryContact({{ $user->id }})" 
                                class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                            Set as Primary
                        </button>
                        @endunless
                        
                        <!-- Dropdown Menu -->
                        <div class="relative">
                            <button onclick="toggleDropdown({{ $user->id }})" 
                                    class="inline-flex items-center p-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"/>
                                </svg>
                            </button>
                            
                            <div id="dropdown-{{ $user->id }}" class="hidden absolute right-0 z-10 mt-2 w-48 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5">
                                <div class="py-1">
                                    <button onclick="showEditUserModal({{ $user->id }}, '{{ $user->pivot->access_level }}', '{{ $user->full_name }}')" 
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Change Access Level
                                    </button>
                                    
                                    <div class="border-t border-gray-100"></div>
                                    
                                    @unless($user->pivot->is_primary_contact)
                                    <button onclick="removeUser({{ $user->id }}, '{{ $user->full_name }}')" 
                                            class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                        Remove from Partner
                                    </button>
                                    @else
                                    <span class="block px-4 py-2 text-sm text-gray-400">
                                        Cannot remove primary contact
                                    </span>
                                    @endunless
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            @endforeach
        </ul>
        @else
        <div class="px-4 py-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No users</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by adding a user to this business partner.</p>
            <div class="mt-6">
                <button onclick="showAddUserModal()" 
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                    </svg>
                    Add User
                </button>
            </div>
        </div>
        @endif
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg max-w-md w-full mx-auto shadow-xl">
            <form action="{{ route('admin.business-partners.add-user', $businessPartner) }}" method="POST">
                @csrf
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Add User to Business Partner</h3>
                    
                    <!-- User Type Selection -->
                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-700">User Type</label>
                        <div class="mt-2 space-y-2">
                            <div class="flex items-center">
                                <input id="existing_user" name="user_type" type="radio" value="existing" onchange="toggleUserFields()" 
                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                <label for="existing_user" class="ml-3 block text-sm text-gray-700">
                                    Add Existing User
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="new_user" name="user_type" type="radio" value="new" onchange="toggleUserFields()" 
                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                <label for="new_user" class="ml-3 block text-sm text-gray-700">
                                    Create New User
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Existing User Selection -->
                    <div id="existingUserFields" class="mb-4 hidden">
                        <label for="user_id" class="block text-sm font-medium text-gray-700">Select User</label>
                        <select name="user_id" id="user_id" 
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Choose a user...</option>
                            @foreach(\App\Models\User::whereDoesntHave('businessPartners', function($query) use ($businessPartner) {
                                $query->where('business_partner_id', $businessPartner->id);
                            })->get() as $availableUser)
                            <option value="{{ $availableUser->id }}">{{ $availableUser->full_name }} ({{ $availableUser->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- New User Fields -->
                    <div id="newUserFields" class="mb-4 hidden space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                                <input type="text" name="first_name" id="first_name" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                                <input type="text" name="last_name" id="last_name" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone (Optional)</label>
                            <input type="text" name="phone" id="phone" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <input type="password" name="password" id="password" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>

                    <!-- Access Level -->
                    <div class="mb-4">
                        <label for="access_level" class="block text-sm font-medium text-gray-700">Access Level</label>
                        <select name="access_level" id="access_level" 
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Select Access Level</option>
                            <option value="business_partner_admin">Business Partner Admin - Full access to partner account</option>
                            <option value="loan_officer">Loan Officer - Create and manage inspection requests</option>
                            <option value="billing_manager">Billing Manager - Manage billing and payments</option>
                            <option value="property_manager">Property Manager - Manage properties and reports</option>
                            <option value="user">User - Standard access</option>
                            <option value="viewer">Viewer - Read-only access</option>
                        </select>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Add User
                    </button>
                    <button type="button" onclick="hideAddUserModal()" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg max-w-md w-full mx-auto shadow-xl">
            <form id="editUserForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Edit User Access Level</h3>
                    
                    <!-- User Info -->
                    <div class="mb-4">
                        <p class="text-sm text-gray-600">
                            Editing access level for: <span id="editUserName" class="font-semibold"></span>
                        </p>
                    </div>

                    <!-- Access Level -->
                    <div class="mb-4">
                        <label for="edit_access_level" class="block text-sm font-medium text-gray-700">Access Level</label>
                        <select name="access_level" id="edit_access_level" 
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="business_partner_admin">Business Partner Admin - Full access to partner account</option>
                            <option value="loan_officer">Loan Officer - Create and manage inspection requests</option>
                            <option value="billing_manager">Billing Manager - Manage billing and payments</option>
                            <option value="property_manager">Property Manager - Manage properties and reports</option>
                            <option value="user">User - Standard access</option>
                            <option value="viewer">Viewer - Read-only access</option>
                        </select>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Update Access Level
                    </button>
                    <button type="button" onclick="hideEditUserModal()" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Modal functions
function showAddUserModal() {
    document.getElementById('addUserModal').classList.remove('hidden');
}

function hideAddUserModal() {
    document.getElementById('addUserModal').classList.add('hidden');
    // Reset form
    document.querySelector('#addUserModal form').reset();
    // Hide all conditional fields
    document.getElementById('existingUserFields').classList.add('hidden');
    document.getElementById('newUserFields').classList.add('hidden');
}

function showEditUserModal(userId, currentAccessLevel, userName) {
    const modal = document.getElementById('editUserModal');
    const form = document.getElementById('editUserForm');
    const nameSpan = document.getElementById('editUserName');
    const accessSelect = document.getElementById('edit_access_level');
    
    // Set form action
    form.action = `/admin/business-partners/{{ $businessPartner->id }}/users/${userId}/update-access`;
    
    // Set user name
    nameSpan.textContent = userName;
    
    // Set current access level
    accessSelect.value = currentAccessLevel;
    
    // Show modal
    modal.classList.remove('hidden');
}

function hideEditUserModal() {
    document.getElementById('editUserModal').classList.add('hidden');
}

// Toggle user type fields
function toggleUserFields() {
    const existingUser = document.getElementById('existing_user').checked;
    const newUser = document.getElementById('new_user').checked;
    
    const existingFields = document.getElementById('existingUserFields');
    const newFields = document.getElementById('newUserFields');
    
    if (existingUser) {
        existingFields.classList.remove('hidden');
        newFields.classList.add('hidden');
    } else if (newUser) {
        existingFields.classList.add('hidden');
        newFields.classList.remove('hidden');
    } else {
        existingFields.classList.add('hidden');
        newFields.classList.add('hidden');
    }
}

// Dropdown functions
function toggleDropdown(userId) {
    const dropdown = document.getElementById('dropdown-' + userId);
    dropdown.classList.toggle('hidden');
    
    // Close other dropdowns
    document.querySelectorAll('[id^="dropdown-"]').forEach(function(element) {
        if (element.id !== 'dropdown-' + userId) {
            element.classList.add('hidden');
        }
    });
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick^="toggleDropdown"]')) {
        document.querySelectorAll('[id^="dropdown-"]').forEach(function(element) {
            element.classList.add('hidden');
        });
    }
});

// User management functions
function setPrimaryContact(userId) {
    if (!confirm('Are you sure you want to set this user as the primary contact?')) {
        return;
    }

    fetch(`/admin/business-partners/{{ $businessPartner->id }}/users/${userId}/set-primary`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => {
        
        window.location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
        // Even if there's an error, reload to see if it actually worked
        window.location.reload();
    });
}

function removeUser(userId, userName) {
    if (!confirm(`Are you sure you want to remove ${userName} from this business partner?`)) {
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/business-partners/{{ $businessPartner->id }}/users/${userId}/remove`;
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
    
    const methodField = document.createElement('input');
    methodField.type = 'hidden';
    methodField.name = '_method';
    methodField.value = 'DELETE';
    
    form.appendChild(csrfToken);
    form.appendChild(methodField);
    document.body.appendChild(form);
    form.submit();
}

// Close modals with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideAddUserModal();
        hideEditUserModal();
    }
});
</script>
@endpush

@endsection