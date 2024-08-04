@extends('layouts.app')
@section('main')
<div class="container">
    <div class="row my-5">
        @include('layouts.sidebar')
        <div class="col-md-9">
            @include('layouts.message')
            <div class="card border-0 shadow">
                <div class="card-header  text-white">
                    Edit User
                </div>
                <div class="card-body">
                    <form action="{{ route('users.update',$user->id) }}" enctype="multipart/form-data" method="post">
                        @csrf
                        <div class="row gy-3 overflow-hidden">
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" placeholder="Name" value="{{ old('name', $user->name) }}">
                                    <label for="name" class="form-label">Name</label>
                                    @error('name')
                                    <p class="invalid-feedback">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control @error('email') is-invalid @enderror" name="email" id="email" placeholder="name@example.com" value="{{ old('email', $user->email) }}">
                                    <label for="email" class="form-label">Email</label>
                                    @error('email')
                                    <p class="invalid-feedback">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <select name="role" id="role" class="form-control @error('role') is-invalid @enderror">
                                        <option value="">Select Role</option>
                                        <option value="admin" {{ $user->role == 'admin'? 'selected':'' }}>Admin</option>
                                        <option value="user" {{ $user->role == 'user'? 'selected':'' }}>User</option>
                                    </select>
                                    <label for="role" class="form-label">Role</label>
                                    @error('role')
                                    <p class="invalid-feedback">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password" placeholder="Password">
                                    <label for="password" class="form-label">Password</label>
                                    @error('password')
                                    <p class="invalid-feedback">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    @error('password_confirmation')
                                    <p class="invalid-feedback">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-grid">
                                    <button class="btn bsb-btn-xl btn-primary py-3" type="submit">Register Now</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
