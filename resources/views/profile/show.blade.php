@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-user"></i> Profile</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        @if($profile && $profile->avatar)
                            <img src="/storage/{{ $profile->avatar }}" 
                                 class="rounded-circle mb-3" width="150" height="150" alt="Avatar">
                        @else
                            <i class="fas fa-user-circle mb-3" style="font-size: 150px; color: #6c757d;"></i>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <h5>{{ $user->display_name }}</h5>
                        <p class="text-muted">{{ $user->email }}</p>
                        
                        @if($profile && $profile->bio)
                            <p>{{ $profile->bio }}</p>
                        @endif
                        
                        <div class="mb-3">
                            <span class="badge bg-{{ $profile && $profile->status === 'online' ? 'success' : 'secondary' }}">
                                {{ $profile ? ucfirst($profile->status) : 'Offline' }}
                            </span>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit Profile
                            </a>
                            <a href="{{ route('rooms.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Rooms
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 