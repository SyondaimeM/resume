@extends('admin.layouts.admin_app')

@section('content')
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{$user->firstName."'s Profile"}} </h4>
                <form class="form-sample" id="profileForm"
                      action="{{'/admin/profile/update/'. $user->id}}"
                      method="POST" enctype="multipart/form-data">
                    @csrf
                    <p class="card-description">
                        Info
                    </p>
                    {{--                    <div class="row">
                                            <div class="col-md-3" style="margin-left: 75%;">
                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">Preview</label>
                                                    <div class="col-sm-9">
                                                        <div class="preview-image"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>--}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="firstName">First Name</label>
                                <div class="col-sm-9">
                                    <input type="text" id="firstName" name="firstName"
                                           value="{{$user->firstName}}"
                                           class="form-control"/>
                                    @if ($errors->has('firstName'))
                                        <div class="alert alert-danger">
                                            <li>{{ $errors->first('firstName') }}</li>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Last Name</label>
                                <div class="col-sm-9">
                                    <input type="text" name="lastName"
                                           value="{{$user->lastName}}"
                                           class="form-control"/>
                                    @if ($errors->has('lastName'))
                                        <div class="alert alert-danger">
                                            <li>{{ $errors->first('lastName') }}</li>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="email">Email</label>
                                <div class="col-sm-9">
                                    <input type="email" id="email" name="email"
                                           value="{{$user->email}}"
                                           class="form-control"/>
                                    @if ($errors->has('email'))
                                        <div class="alert alert-danger">
                                            <li>{{ $errors->first('email') }}</li>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Phone Number</label>
                                <div class="col-sm-9">
                                    <input type="tel" name="phone_number" placeholder="1-555-555-5555"
                                           value="{{$user->phone_number}}"
                                           class="form-control"/>
                                    @if ($errors->has('phone_number'))
                                        <div class="alert alert-danger">
                                            <li>{{ $errors->first('phone_number') }}</li>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="linkedin">LinkedIn</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <input type="url" id="linkedin" name="linkedin"
                                               aria-label="Recipient's username"
                                               value="{{$user->linkedin}}"
                                               class="form-control"/>
                                        <div class="input-group-append">
                                            <button class="btn btn-sm btn-linkedin" type="button">
                                                <i class="ti-linkedin"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @if ($errors->has('linkedin'))
                                        <div class="alert alert-danger">
                                            <li>{{ $errors->first('linkedin') }}</li>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Github</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <input type="url" id="github" name="github" aria-label="Recipient's username"
                                               value="{{$user->github}}"
                                               class="form-control"/>
                                        <div class="input-group-append">
                                            <button class="btn btn-sm btn-github" type="button">
                                                <i class="ti-github"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @if ($errors->has('github'))
                                        <div class="alert alert-danger">
                                            <li>{{ $errors->first('github') }}</li>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="instagram">Instagram</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <input type="url" id="instagram" name="instagram"
                                               aria-label="Recipient's username"
                                               value="{{$user->instagram}}"
                                               class="form-control"/>
                                        <div class="input-group-append">
                                            <button class="btn btn-sm btn-dribbble" type="button">
                                                <i class="ti-instagram"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @if ($errors->has('instagram'))
                                        <div class="alert alert-danger">
                                            <li>{{ $errors->first('instagram') }}</li>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Address</label>
                                <div class="col-sm-9">
                                    <input type="text" name="address"
                                           value="{{$user->address}}" class="form-control"/>
                                    @if ($errors->has('address'))
                                        <div class="alert alert-danger">
                                            <li>{{ $errors->first('address') }}</li>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">File upload</label>
                                <div class="col-sm-9">
                                    <input type="file" id="photo" name="photo" class="file-upload-default">
                                    <div class="input-group col-xs-6">
                                        <input type="text" class="form-control file-upload-info" disabled
                                               placeholder="Upload Image">
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-primary"
                                                    type="button">Upload</button>
                                        </span>
                                    </div>
                                    @if ($errors->has('photo'))
                                        <div class="alert alert-danger">
                                            <li>{{ $errors->first('photo') }}</li>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Preview</label>
                                <div class="col-sm-9">
                                    <div class="preview-image">
                                        <img src="{{ url('/images/'.$user->photo_path) }}" style="max-width: 200px;" alt="" class="image_pr">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 col-form-label" for="summery">Summery</label>
                                <div class="col-sm-12">
                                    <input type="text" id="myTextarea" name="summery"
                                           class="form-control" value="{{isset($user)? $user->summery: ''}}"/>

                                    @if ($errors->has('summery'))
                                        <div class="alert alert-danger">
                                            <li>{{ $errors->first('summery') }}</li>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" id="save" class="btn btn-primary mr-2">Submit</button>
                    <button id="cancel" onclick="editInfo()" class="btn btn-light">Start Edit</button>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(function () {
            // To reach inputs throw a loop.
            // $('#profileForm').find('input').each(function(){
            //     return $(this).attr('readonly', true);
            // });
            $('input').prop('readonly', true);
            $('#save').prop('disabled', true);
            $('#photo').prop('disabled', true);
        });

        function editInfo() {
            $('input').prop('readonly', false);
            $('#save').prop('disabled', false);
            $('#photo').prop('disabled', false);
            $('#cancel').prop('disabled', true);
        }
    </script>
    {{--    Preview Image--}}
    <script type="text/javascript">
        $(function () {
            // Multiple images preview with JavaScript
            var multiImgPreview = function (input, imgPreviewPlaceholder) {
                if (imgPreviewPlaceholder.files) {
                    imgPreviewPlaceholder.files.clear();
                }
                if (input.files) {
                    var filesAmount = input.files.length;
                    for (i = 0; i < filesAmount; i++) {
                        var reader = new FileReader();
                        reader.onload = function (event) {
                            var $clone = $($.parseHTML('<img>'));
                            $clone.attr('class', 'image_pr');
                            $clone.attr('style', 'max-width: 200px;');
                            $clone.attr('src', event.target.result).appendTo(imgPreviewPlaceholder);
                        }
                        reader.readAsDataURL(input.files[i]);
                    }
                }
            };
            $('#photo').on('change', function () {
                // To delete the uploaded photo from preview after selecting new photo
                var arr = document.getElementsByClassName('image_pr');
                if (arr.length) {
                    for (var i = 0; i < arr.length; i++) {
                        arr[i].remove();
                    }
                }
                multiImgPreview(this, 'div.preview-image');
            });
        });
    </script>
@endsection
