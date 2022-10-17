@extends('admin.layouts.admin_app')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <p class="card-title">Experiences Table</p>
                    <a class="btn btn-outline-primary" href="/admin/experience/create">Add new</a>
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>Company Name</th>
                                        <th>Country</th>
                                        <th>Enrollment state</th>
                                        <th>View state</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Duration</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($expSections as $exp)
                                        <tr>
                                            <td style="white-space:pre-wrap; word-wrap:break-word">{{$exp->companyName}}</td>
                                            <td style="white-space:pre-wrap; word-wrap:break-word">{{$exp->section->country. ' / ' .$exp->section->city}}</td>
                                            <td><label
                                                    class="badge {{($exp->section->isActive)? 'badge-info': 'badge-warning'}}">{{($exp->section->isActive)? 'Still There' : 'Resign'}}</label>
                                            </td>
                                            <td><label
                                                    class="badge {{($exp->section->isShown)? 'badge-success': 'badge-secondary'}}">{{($exp->section->isShown)? 'Shown' : 'Hidden'}}</label>
                                            </td>
                                            <td>{{$exp->section->startDate}}</td>
                                            <td>{{($exp->section->isActive)? 'Present': $exp->section->endDate}}</td>
                                            <td>{{date_diff(date_create($exp->section->endDate), date_create($exp->section->startDate))->format('%y year %m month')}}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <form action="/admin/experience/edit/{{ $exp->id }}"
                                                          method="get">
                                                        @csrf
                                                        <button type="submit"
                                                                class="btn btn-dark ti-pencil-alt btn-icon">
                                                        </button>
                                                    </form>
                                                    <form action="/admin/experience/delete/{{ $exp->id }}"
                                                          method="post">
                                                        @method('DELETE')
                                                        @csrf
                                                        <button
                                                            onclick="return confirm('Are you sure You want to delete this?')"
                                                            type="submit" class="btn btn-danger ti-trash btn-icon">
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
