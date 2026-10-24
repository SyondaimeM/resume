@extends('admin.layouts.admin_app')
@section('content')
<div class="card"><div class="card-body">
    <h1 class="h4">Public profile and resume</h1>
    @if(session('success'))<p class="alert alert-success" role="status">{{ session('success') }}</p>@endif
    @if($errors->any())<div class="alert alert-danger" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <p>These fields update the public website. Upload a revised PDF when its contents change; the PDF is not generated automatically.</p>
    <form method="post" action="/admin/portfolio" enctype="multipart/form-data">
        @csrf
        @foreach(['firstName' => 'First name', 'lastName' => 'Last name', 'github' => 'GitHub URL', 'linkedin' => 'LinkedIn URL', 'instagram' => 'Instagram URL'] as $name => $label)
            <div class="form-group"><label for="{{ $name }}">{{ $label }}</label><input class="form-control" id="{{ $name }}" name="{{ $name }}" value="{{ old($name, $user->$name) }}"></div>
        @endforeach
        <div class="form-group"><label for="summery">Public summary</label><textarea class="form-control" rows="5" id="summery" name="summery">{{ old('summery', $user->summery) }}</textarea></div>
        <div class="form-group"><label for="roles">Rotating roles (one per line)</label><textarea class="form-control" rows="4" id="roles" name="roles">{{ old('roles', implode("\n", $user->roles ?? config('portfolio.profile.roles'))) }}</textarea></div>
        <div class="form-group"><label for="resume">Updated resume PDF (up to 10 MB)</label><input type="file" id="resume" name="resume" accept="application/pdf"></div>
        <p><a href="{{ asset($user->resume_path ?? config('portfolio.resume')) }}">Current resume</a></p>
        <button class="btn btn-primary">Save public profile</button>
    </form>
</div></div>
@endsection
