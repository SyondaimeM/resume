@extends('admin.layouts.admin_app')

@section('content')
    <div class="row">
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Skills</h4>
                    <p class="card-description">
                        Skills in Blue are shown in the resume.
                    </p>
                    <div class="template-demo">
                        <div class="btn-group" style="display: grid; grid-template-columns: repeat(auto-fill,250px);">
                            @foreach($skills as $skill)
                                <div class="dropdown" style="padding: 10px; display: grid;">
                                    <button class="btn {{$skill->isShown? 'btn-info': 'btn-secondary'}} dropdown-toggle"
                                            type="button"
                                            id="dropdownMenuSizeButton2" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                        {{$skill->name}}
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuSizeButton2">
                                        <a class="dropdown-item"
                                           onclick='fillBoxes(encodeURIComponent(JSON.stringify({{$skill}})))'>Update</a>
                                        <form action="/admin/skill/delete/{{$skill->id}}" method="post">
                                            @method('DELETE')
                                            @csrf
                                            <button onclick="return confirm('Are you sure You want to delete this?')"
                                                    type="submit"
                                                    class="dropdown-item" href="">Delete
                                            </button>
                                        </form>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="#">Separated link</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 id="formTitle" class="card-title">Add new</h4>
                    <form class="forms-sample" name="skillForm" action="/admin/skill/store"
                          method="POST" enctype="multipart/form-data" autocomplete="on">
                        @csrf
                        <div class="form-group row">
                            <label for="name" class="col-sm-3 col-form-label">Name</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="name" name="name" value="{{old('name')}}">
                                @if ($errors->has('name'))
                                    <div class="alert alert-danger">
                                        <li>{{ $errors->first('name') }}</li>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="description" class="col-sm-3 col-form-label">Description</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="description" name="description"
                                       value="{{old('description')}}">
                                @if ($errors->has('description'))
                                    <div class="alert alert-danger">
                                        <li>{{ $errors->first('description') }}</li>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Status</label>
                            <div class="col-sm-3">
                                <select class="form-control form-control-sm" name="isShown" id="isShown">
                                    <option value="1" selected>Shown</option>
                                    <option value="0">Hidden</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="user_id" value="{{Auth::user()->id}}">
                        <button type="submit" id="submitBtn" class="btn btn-primary mr-2">Add</button>
                        <input type="reset" class="btn btn-light" value="Clear">
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Another way to show the skill list as a table.--}}
    {{--<div class="row">
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Skills Table</h4>
                    <a class="btn btn-outline-primary" href="/admin/skill/create">Add new Skill</a>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($skills as $skill)
                                <tr>
                                    <td>{{$skill->name}}</td>
                                    <td>{{$skill->description}}</td>
                                    <td><label class="badge {{isset($skill->isShown)? 'badge-info' : 'badge-danger'}}">Pending</label>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="submit"
                                                    onclick='fillBoxes(encodeURIComponent(JSON.stringify({{$skill}})))'
                                                    class="btn btn-dark ti-pencil-alt btn-icon">
                                            </button>
                                            <form action="/admin/skill/delete/{{ $skill->id }}"
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
    </div>--}}
    <script>
        //toggle between add and update.
        //turn form into add form on reset.
        document.skillForm.addEventListener('reset', () => {
            document.querySelector('#submitBtn').innerText = "Add";
            document.querySelector('#formTitle').innerText = "Add new";
            document.skillForm.action = "/admin/skill/store";
        });

        //fill inputs on editing skill.
        function fillBoxes(obj) {
            curr = JSON.parse(decodeURIComponent(obj))
            $('#name').val(curr.name);
            $('#description').val(curr.description);
            document.getElementById('isShown').value = curr.isShown;
            document.querySelector('#submitBtn').innerText = "Update";
            document.querySelector('#formTitle').innerText = "Update skill";
            document.skillForm.action = "/admin/skill/update/" + curr.id;
        }


        /*        Todo: update the skill table on adding or editing.
                function editskill($s) {
                    const xhttp = new XMLHttpRequest();
                    xhttp.onreadystatechange = function () {
                        if (this.readyState === 4 && this.status === 200) {
                            document.getElementById("").innerHTML = this.responseText;
                        }
                    }
                }*/

    </script>
@endsection
