@extends('admin.layouts.admin_app')
@section('content')
<div class="card"><div class="card-body">
    <h1 class="h4">{{ ucfirst($kind) }}</h1>
    @if(session('success'))<p class="alert alert-success" role="status">{{ session('success') }}</p>@endif
    <p>Changes appear on the public website immediately. Lower order numbers appear first. Hidden records remain stored.</p>
    <a class="btn btn-primary mb-3" href="/admin/{{ $kind }}/create">Add {{ $kind }}</a>
    <div class="table-responsive"><table class="table">
        <thead><tr><th>Name</th><th>Visible</th><th>Order</th><th>Actions</th></tr></thead>
        <tbody>
        @foreach($items as $item)
            @php($sectional = in_array($kind, ['experience', 'education'], true))
            <tr>
                <td>{{ $item->companyName ?? $item->collageName ?? $item->name }}</td>
                <td>{{ ($sectional ? $item->section->isShown : $item->isShown) ? 'Shown' : 'Hidden' }}</td>
                <td>{{ $sectional ? $item->section->sort_order : $item->sort_order }}</td>
                <td>
                    <a class="btn btn-sm btn-primary" href="/admin/{{ $kind }}/edit/{{ $item->id }}">Edit</a>
                    <form class="d-inline" action="/admin/{{ $kind }}/delete/{{ $item->id }}" method="post" onsubmit="return confirm('Permanently delete this record? Hide it to retain the data instead.')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table></div>
    {{ $items->links() }}
</div></div>
@endsection
