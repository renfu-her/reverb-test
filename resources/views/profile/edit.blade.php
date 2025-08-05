@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-edit"></i> Edit Profile</h4>
            </div>
            <div class="card-body">
                <form id="profileForm" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            @if($profile && $profile->avatar)
                                <img src="/storage/{{ $profile->avatar }}" 
                                     class="rounded-circle mb-3" width="150" height="150" alt="Avatar" id="avatarPreview">
                            @else
                                <i class="fas fa-user-circle mb-3" style="font-size: 150px; color: #6c757d;" id="avatarPreview"></i>
                            @endif
                            
                            <div class="mb-3">
                                <label for="avatar" class="form-label">Avatar</label>
                                <input type="file" class="form-control" id="avatar" name="avatar" 
                                       accept="image/*">
                                <div class="form-text">Upload a profile picture (optional, max 2MB)</div>
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="display_name" class="form-label">Display Name *</label>
                                <input type="text" class="form-control" id="display_name" name="display_name" 
                                       value="{{ $profile ? $profile->display_name : $user->name }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea class="form-control" id="bio" name="bio" rows="3" 
                                          placeholder="Tell us about yourself...">{{ $profile ? $profile->bio : '' }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="online" {{ $profile && $profile->status === 'online' ? 'selected' : '' }}>Online</option>
                                    <option value="away" {{ $profile && $profile->status === 'away' ? 'selected' : '' }}>Away</option>
                                    <option value="busy" {{ $profile && $profile->status === 'busy' ? 'selected' : '' }}>Busy</option>
                                    <option value="offline" {{ $profile && $profile->status === 'offline' ? 'selected' : '' }}>Offline</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('profile.show') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Profile
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Avatar preview
    $('#avatar').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#avatarPreview').replaceWith('<img src="' + e.target.result + '" class="rounded-circle mb-3" width="150" height="150" alt="Avatar" id="avatarPreview">');
            };
            reader.readAsDataURL(file);
        }
    });
    
    $('#profileForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData();
        formData.append('display_name', $('#display_name').val());
        formData.append('bio', $('#bio').val());
        formData.append('status', $('#status').val());
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        
        // Add avatar if selected
        const avatarFile = $('#avatar')[0].files[0];
        if (avatarFile) {
            formData.append('avatar', avatarFile);
        }
        
        $.ajax({
            url: '{{ route("profile.update") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert('Profile updated successfully!');
                    window.location.href = '{{ route("profile.show") }}';
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    let errorMessage = 'Please fix the following errors:\n';
                    Object.keys(errors).forEach(key => {
                        errorMessage += `• ${errors[key][0]}\n`;
                    });
                    alert(errorMessage);
                } else {
                    alert('Failed to update profile. Please try again.');
                }
            }
        });
    });
});
</script>
@endpush
@endsection
