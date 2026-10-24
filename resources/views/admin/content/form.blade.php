@extends('admin.layouts.admin_app')
@section('content')
@php
    $fields = ['sort_order' => 'Display order'];
    $multiline = ['details', 'courses', 'achievements', 'body', 'technologies', 'context'];
    if (in_array($kind, ['experience', 'education'], true)) {
        $fields += ['title' => 'Title', 'country' => 'Country', 'city' => 'City / location', 'startDate' => 'Start date', 'endDate' => 'End date (leave blank if current)', 'display_period' => 'Display period (optional, e.g. 2015-2020)', 'details' => 'Details (one bullet per line)'];
    }
    if ($kind === 'experience') {
        $fields += ['companyName' => 'Company', 'brand' => 'Text wordmark', 'image' => 'Existing public image path (images/...)'];
    } elseif ($kind === 'education') {
        $fields += ['collageName' => 'Institution', 'degree' => 'Qualification / degree', 'department' => 'Department', 'gpa' => 'GPA (optional; not shown publicly)', 'courses' => 'Courses (one per line)', 'achievements' => 'Activities and achievements (one per line)', 'image' => 'Existing public image path (images/...)'];
    } elseif ($kind === 'skill') {
        $fields += ['name' => 'Skill', 'description' => 'Description', 'group_name' => 'Group name', 'group_description' => 'Group description', 'group_order' => 'Group order'];
    } else {
        $fields += ['name' => 'Project title', 'body' => 'Description', 'context' => 'Public product context (optional)', 'technologies' => 'Technologies (one per line)', 'link' => 'Public HTTPS / HTTP URL', 'link_label' => 'Link label', 'image' => 'Existing public image path (images/...)', 'brand' => 'Text banner (if no image)'];
    }
    $toggles = ['isShown' => 'Visible on website'];
    if (in_array($kind, ['experience', 'education'], true)) $toggles['isActive'] = 'Currently here';
    if ($kind === 'project') $toggles['featured'] = 'Selected project (otherwise additional project)';
@endphp
<div class="card"><div class="card-body">
    <h1 class="h4">{{ $id ? 'Edit' : 'Add' }} {{ $kind }}</h1>
    @if($errors->any())<div class="alert alert-danger" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form action="/admin/{{ $kind }}/{{ $id ? 'update/'.$id : 'store' }}" method="post" enctype="multipart/form-data">
        @csrf
        @foreach($fields as $name => $label)
            <div class="form-group">
                <label for="{{ $name }}">{{ $label }}</label>
                @if(in_array($name, $multiline, true))
                    <textarea class="form-control" rows="5" id="{{ $name }}" name="{{ $name }}">{{ old($name, $values[$name] ?? '') }}</textarea>
                @else
                    <input class="form-control" id="{{ $name }}" name="{{ $name }}" type="{{ in_array($name, ['startDate', 'endDate'], true) ? 'date' : (in_array($name, ['sort_order', 'group_order'], true) ? 'number' : 'text') }}"
                        value="{{ old($name, $values[$name] ?? (in_array($name, ['sort_order', 'group_order'], true) ? 0 : '')) }}">
                @endif
                @if($name === 'display_period')
                    <small class="form-text text-muted">Leave blank to use the dates automatically. An unchanged custom label is cleared when dates or current status change.</small>
                @elseif($name === 'group_description' || $name === 'group_order')
                    <small class="form-text text-muted">Applies to every skill with this group name.</small>
                @endif
            </div>
        @endforeach
        @if($kind !== 'skill')
            <div class="form-group">
                <label for="image_upload">Upload a logo or project image</label>
                <input type="file" class="form-control-file" id="image_upload" name="image_upload" accept="image/jpeg,image/png,image/webp">
                <small class="form-text text-muted">JPEG, PNG, or WebP, up to 5 MB. An upload replaces the image path above. Existing image files are retained.</small>
                @if(!empty($values['image']))
                    <img src="{{ asset($values['image']) }}" alt="Current image" width="160" height="100" style="object-fit:contain;max-width:100%">
                @endif
            </div>
        @endif
        @foreach($toggles as $name => $label)
            <div class="form-group">
                <label for="{{ $name }}">{{ $label }}</label>
                <select class="form-control" id="{{ $name }}" name="{{ $name }}">
                    <option value="1" @selected(old($name, $values[$name] ?? ($name !== 'isActive')) == 1)>Yes</option>
                    <option value="0" @selected(old($name, $values[$name] ?? ($name !== 'isActive')) == 0)>No</option>
                </select>
            </div>
        @endforeach
        <button class="btn btn-primary">Save</button>
        <a class="btn btn-light" href="/admin/{{ $kind }}">Cancel</a>
    </form>
</div></div>
@endsection
