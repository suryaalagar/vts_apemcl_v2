@extends('layouts.app')
@section('content')
    <!-- <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Basic Elements</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-xl-4 col-lg-6 col-md-12 mb-1">
                                            <fieldset class="form-group">
                                                <label for="basicInput">Company Name</label>
                                                <input type="text" class="form-control" id="basicInput">
                                            </fieldset>
                                        </div>

                                        <div class="col-xl-4 col-lg-6 col-md-12 mb-1">
                                            <fieldset class="form-group">
                                                <label for="basicInput">Email</label>
                                                <input type="text" class="form-control" id="basicInput">
                                            </fieldset>
                                        </div>

                                    </div>

                                    <div class="row">
                                    <input type="reset" class="form-control btn-danger btn-sm">
                                    <input type="submit" class="form-control btn-success btn-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Settings</h4>
                    <a class="heading-elements-toggle"><i class="feather icon-align-justify font-medium-3"></i></a>

                </div>
                <div class="card-content collapse show">
                    <div class="card-body">
                        <form action="{{ route('setting.update', $setting->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-xl-4 col-lg-6 col-md-12 mb-1">
                                        <fieldset class="form-group">
                                            <label for="basicInput">Company Name</label>
                                            <input type="text" id="name" name="name" value="{{ $setting->name }}"
                                                class="form-control" id="basicInput">
                                            @error('name')
                                                <div>
                                                    <strong style="color:red ">{{ $message }}</strong>
                                                </div>
                                            @enderror
                                        </fieldset>
                                    </div>

                                    <div class="col-xl-4 col-lg-6 col-md-12 mb-1">
                                        <fieldset class="form-group">
                                            <label for="basicInput">Email</label>
                                            <input id="email" type="text" name="email"
                                                value="{{ $setting->email }}" class="form-control" id="basicInput">
                                            @error('email')
                                                <div>
                                                    <strong style="color:red ">{{ $message }}</strong>
                                                </div>
                                            @enderror
                                        </fieldset>

                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="text-right">
                                    <button type="reset" class="btn btn-warning">Reset <i
                                            class="feather icon-refresh-cw position-right"></i></button>
                                    <button type="submit" class="btn btn-primary">Submit <i
                                            class="feather icon-thumbs-up position-right"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
