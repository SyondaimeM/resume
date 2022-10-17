@extends('admin.layouts.admin_app')

@section('content')
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{isset($experience)? 'Edit' : 'Add New'}} Experiences Section</h4>
                <form class="form-sample" action="{{isset($experience)? '/admin/experience/update/'. $experience->id : '/admin/experience/store'}}"
                      method="POST" enctype="multipart/form-data">
                    @csrf
                    <p class="card-description">
                    </p>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="title">Title</label>
                                <div class="col-sm-9">
                                    <input type="text" id="title" name="title"
                                           value="{{isset($experience)? $experience->section->title : old('title')}}"
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
                                <label class="col-sm-3 col-form-label">Company Name</label>
                                <div class="col-sm-9">
                                    <input type="text" name="companyName"
                                           value="{{isset($experience)? $experience->companyName : old('companyName')}}"
                                           class="form-control"/>
                                    @if ($errors->has('companyName'))
                                        <div class="alert alert-danger">
                                            <li>{{ $errors->first('companyName') }}</li>
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
                                <label class="col-sm-3 col-form-label">Start Date</label>
                                <div class="col-sm-9">
                                    <input type="date" class="form-control" name="startDate"
                                           value="{{isset($experience)? $experience->section->startDate : old('startDate')}}"
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
                                    <input type="date" class="form-control" name="endDate" id="endDate"
                                           value="{{isset($experience)? $experience->section->endDate : old('endDate')}}"
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
                                <label class="col-sm-3 col-form-label">Status</label>
                                <div class="col-sm-4">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" name="isActive"
                                                   @checked(isset($experience)? $experience->section->isActive : old('isActive'))
                                                   id="activeCheckBox" onclick="check()">
                                            Still here?
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" name="isShown"
                                                   @checked(isset($experience)? $experience->section->isShown : old('isShown'))
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
                                <label class="col-sm-1 col-form-label" for="details">Details</label>
                                <div class="col-sm-11">
                                    <input type="text" id="myTextarea" name="details"
                                           value="{{isset($experience)? $experience->section->details : old('details')}}"
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
                    <a href="/admin/experience" class="btn btn-light">Cancel</a>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function check() {
            var element = document.getElementById('endDate');
            element.disabled = document.getElementById('activeCheckBox').checked;
            element.value = null;
        }
    </script>
@endsection
