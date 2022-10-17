@extends('admin.layouts.admin_app')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <p class="card-title">Education Table</p>
                    <a class="btn btn-outline-primary" href="/admin/education/create">Add new</a>
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>Collage Name</th>
                                        <th>Degree</th>
                                        <th>Department</th>
                                        <th >Enrollment state</th>
                                        <th>View state</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($eduSections as $section)
                                        <tr>
                                            <td style="white-space:pre-wrap; word-wrap:break-word">{{$section->collageName}}</td>
                                            <td>{{$section->degree}}</td>
                                            <td style="white-space:pre-wrap; word-wrap:break-word">{{$section->department}}</td>
                                            <td ><label class="badge {{($section->section->isActive)? 'badge-warning': 'badge-info'}}">{{($section->section->isActive)? 'Still Student' : 'Graduated'}}</label></td>
                                            <td><label class="badge {{($section->section->isShown)? 'badge-success': 'badge-secondary'}}">{{($section->section->isShown)? 'Shown' : 'Hidden'}}</label></td>
                                            <td>{{$section->section->startDate}}</td>
                                            <td>{{($section->section->isActive)? 'Present': $section->section->endDate}}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <form action="/admin/education/edit/{{ $section->id }}" method="get">
                                                        @csrf
                                                        <button type="submit" class="btn btn-dark ti-pencil-alt btn-icon">
                                                            </button>
                                                    </form>
                                                    <form action="/admin/education/delete/{{ $section->id }}" method="post">
                                                        @method('DELETE')
                                                        @csrf
                                                        <button onclick="return confirm('Are you sure You want to delete this?')" type="submit" class="btn btn-danger ti-trash btn-icon">
                                                            </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
