@extends('layouts.auth')

@section('content')
    @push('styles')
        <style>
            .form-control-position {
                top: 15px !important;
            }

            ,
            #backgroundImage {
                background-image: url('/storage/images/backgrounds/Coworking.JPG');
                width: 100vh;
                height: 100vh;
            }
        </style>
    @endpush
    <section class="row flexbox-container">
        <div class="col-12 d-flex align-items-center justify-content-center">
            <div class="col-lg-4 col-md-8 col-10 box-shadow-2 p-0">
                <div class="card border-grey border-lighten-3 m-0">
                    <div class="card-header border-0">
                        <div class="card-title text-center">
                            <div class="p-1"><img src="{{ asset('img/APEMC.png') }}" alt="logo"></div>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-body pt-0">
                            <form class="form-horizontal" action="{{ route('login') }}" method="post">
                                @csrf
                                <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}
                                    <span style="color:crimson"> *</span></label>
                                <fieldset class="form-group position-relative has-icon-left">
                                    <input id="email" type="email"
                                        class="form-control @error('email') is-invalid @enderror" name="email"
                                        value="{{ old('email') }}" required autocomplete="email" autofocus>

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <div class="form-control-position" style="top:15px !important;">
                                        <i class="fa fa-envelope"></i>
                                    </div>
                                </fieldset>
                                <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}<span
                                        style="color:crimson"> *</span></label>
                                <fieldset class="form-group position-relative has-icon-left">
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        required autocomplete="current-password">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <div class="form-control-position" style="top:15px !important;">
                                        <i class="fa fa-key"></i>
                                    </div>
                                </fieldset>
                                <div class="form-group row">
                                    <div class="col-sm-6 col-12 text-center text-sm-left">
                                        <fieldset>
                                            <div class="icheckbox_square-blue" style="position: relative;">
                                                <input type="checkbox" id="remember-me" class="chk-remember"
                                                    style="position: absolute; opacity: 0;"><ins class="iCheck-helper"
                                                    style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                                            </div>
                                            <label for="remember-me"> {{ __('Remember Me') }}</label>
                                        </fieldset>
                                    </div>
                                    <div class="col-sm-6 col-12 float-sm-left text-center text-sm-right"><a
                                            href="recover-password.html" class="card-link">Forgot
                                            Password?</a></div>
                                </div>
                                <button type="submit" class="btn btn-outline-primary btn-block"><i
                                        class="feather icon-unlock"></i> {{ __('Login') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
