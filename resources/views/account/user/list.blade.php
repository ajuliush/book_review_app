@extends('layouts.app')
<style>
    .lightbox .lb-image {
        max-width: 80%;
        /* Adjust max-width as needed */
        max-height: 80%;
        /* Adjust max-height as needed */
    }

    @media (min-width: 768px) {
        .lightbox .lb-image {
            max-width: 60%;
            /* Adjust max-width for larger screens */
            max-height: 60%;
            /* Adjust max-height for larger screens */
        }
    }

</style>
@section('main')

<div class="container">
    <div class="row my-5">
        @include('layouts.sidebar')
        <div class="col-md-9">
            @include('layouts.message')
            <div class="card border-0 shadow">
                <div class="card-header  text-white">
                    Users
                </div>
                <div class="card-body pb-0">
                    <div class="d-flex justify-content-between">
                        <a href="{{route('users.create')}}" class="btn btn-primary">Add User</a>
                        <form action="" method="get">
                            <div class="d-flex">
                                <input type="text" class="form-control" name="keyword" value="{{Request::get('keyword')}}" placeholder="Keyword">
                                <button type="submit" class="btn btn-primary ms-2">Search</button>
                                <a href="{{route('users.index')}}" class="btn btn-success ms-2">Clear</a>
                            </div>
                        </form>
                    </div>
                    <table class="table  table-striped mt-3">
                        <thead class="table-dark">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Image</th>
                                <th>Created At</th>
                                <th width="100">Action</th>
                            </tr>
                        <tbody>
                            @if($users->isNotEmpty())
                            @foreach($users as $user)
                            <tr>
                                <td>{{$user->name}}</td>
                                <td>{{$user->email}}</td>
                                <td>{{$user->role}}</td>
                                <td>
                                    <a href="{{ asset('uploads/profile_images/' . $user->profile_image) }}" data-lightbox="profile-image">
                                        <img src="{{ asset('uploads/profile_images/' . $user->profile_image) }}" alt="Profile Image" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                                    </a>
                                </td>
                                <td> {{\Carbon\Carbon::parse($user->created_at)->format('d M, Y')}} </td>
                                <td>
                                    <a href="{{route('users.edit',$user->id)}}" class="btn btn-primary btn-sm"><i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                    @if ($user->role != 'admin')
                                    <a href="#" onclick="deleteUser({{$user->id}});" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="5">
                                    Book Not found
                                </td>
                            </tr>
                            @endif
                        </tbody>
                        </thead>
                    </table>
                    @if($users->isNotEmpty())
                    {{ $users->links('layouts.custom-pagination') }}
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    function deleteUser(id) {
        if (confirm('Are you sure you want to delete this')) {
            $.ajax({
                url: '{{route("users.destroy")}}'
                , type: 'delete'
                , data: {
                    id: id
                }
                , headers: {
                    'X-CSRF-TOKEN': '{{csrf_token()}}'
                , }
                , success: function(response) {
                    window.location.href = '{{route("users.index")}}';
                }
            });

        }
    }
    lightbox.option({
        'resizeDuration': 200
        , 'wrapAround': true
    })

</script>
@endsection
