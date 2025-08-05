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
                <form id="profileForm">
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
    $('#profileForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            display_name: $('#display_name').val(),
            bio: $('#bio').val(),
            status: $('#status').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        $.ajax({
            url: '{{ route("profile.update") }}',
            method: 'PUT',
            data: formData,
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
