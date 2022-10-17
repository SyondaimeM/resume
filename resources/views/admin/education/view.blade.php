@extends('admin.layouts.admin_app')

@section('content')
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{isset($education)? 'Edit': 'Add New'}} Education Section</h4>
                <form class="form-sample"
                      action="{{isset($education) ? '/admin/education/update/'. $education->id: '/admin/education/store'}}"
                      method="POST" enctype="multipart/form-data">
                    @csrf
                    <p class="card-description">
                        Info
                    </p>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="title">Title</label>
                                <div class="col-sm-9">
                                    <input type="text" id="title" name="title"
                                           value="{{isset($education)? $education->section->title: old('title')}}"
                                           class="form-control"/>
                                    @if ($errors->has('title'))
                                        <div class="alert alert-danger">
                                            <li>{{ $errors->first('title') }}</li>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Collage Name</label>
                                <div class="col-sm-9">
                                    <input type="text" name="collageName"
                                           value="{{isset($education)? $education->collageName : old('collageName')}}"
                                           class="form-control"/>
                                    @if ($errors->has('collageName'))
                                        <div class="alert alert-danger">
                                            <li>{{ $errors->first('collageName') }}</li>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Country</label>
                                <div class="col-sm-9">
                                    <select class="js-example-basic-single w-100" name="country" id="country">
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">City</label>
                                <div class="col-sm-9">
                                    <select class="js-example-basic-single w-100" name="city" id="city">
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Degree</label>
                                <div class="col-sm-9">
                                    <select class="form-control" name="degree">
                                        <option value="Associate Degree">Associate</option>
                                        <option value="Bachelor Degree">Bachelor's</option>
                                        <option value="Master Degree">Master's</option>
                                        <option value="Doctoral Degree">Doctoral</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        {{--Todo: use an Api to provide departments.--}}
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Department</label>
                                <div class="col-sm-9">
                                    <select class="form-control" name="department">

                                        <option value="Computer Engineering">Computer Engineering</option>
                                        <option value="Computer Engineering">Computer Engineering</option>
                                        <option value="">Category2</option>
                                        <option value="">Category3</option>
                                        <option value="">Category4</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Start Date</label>
                                <div class="col-sm-9">
                                    <input type="date" class="form-control" name="startDate"
                                           value="{{isset($education)? $education->section->startDate: old('startDate')}}"
                                           placeholder="dd/mm/yyyy"/>
                                    @if ($errors->has('startDate'))
                                        <div class="alert alert-danger">
                                            <li>{{ $errors->first('startDate') }}</li>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Finish Date</label>
                                <div class="col-sm-9">
                                    <input id="endDate" type="date" class="form-control" name="endDate" onload="check()"
                                           value="{{isset($education)? $education->section->endDate : old('endDate')}}"
                                           placeholder="dd/mm/yyyy"/>
                                    @if ($errors->has('endDate'))
                                        <div class="alert alert-danger">
                                            <li>{{ $errors->first('endDate') }}</li>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">GPA</label>
                                <div class="col-sm-9">
                                    <input type="decimal" name="gpa"
                                           value="{{isset($education)? $education->gpa : old('gpa')}}"
                                           class="form-control"/>
                                    @if ($errors->has('gpa'))
                                        <div class="alert alert-danger">
                                            <li>{{ $errors->first('gpa') }}</li>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Status</label>
                                <div class="col-sm-4">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" name="isActive"
                                                   @checked(isset($education)? $education->section->isActive : old('isActive'))
                                                   id="activeCheckBox" onclick="check()">
                                            Still here?
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" name="isShown"
                                                   @checked(isset($education)? $education->section->isShown : old('isShown'))
                                                   id="showCheckBox">
                                            Show?
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-sm-1 col-form-label">Details</label>
                                <div class="col-sm-11">
                                    <input type="text" id="myTextarea" name="details"
                                           value="{{isset($education)? $education->section->details : old('details')}}"
                                           class="form-control"/>
                                    @if ($errors->has('details'))
                                        <div class="alert alert-danger">
                                            <li>{{ $errors->first('details') }}</li>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mr-2">Submit</button>
                    <a href="/admin/education" class="btn btn-light">Cancel</a>
                </form>
            </div>
        </div>
    </div>
    <script>

        function check() {
            var element = document.getElementById('endDate');
            element.disabled = document.getElementById('activeCheckBox').checked;
            element.value = null;
        }
    </script>

@endsection
