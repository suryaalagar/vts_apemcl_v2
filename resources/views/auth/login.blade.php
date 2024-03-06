@extends('layouts.auth')

@section('content')
    @push('styles')
        <style>
            .form-control-position {
                top: 15px !important;
            }
        </style>
    @endpush

    <body class="page_bg" style="background: linear-gradient(rgba(0, 0, 0, .8), rgba(0, 0, 0, .8)), url('{{ asset('img/truck.jpg') }}'); height: 100%; background-position: center; background-repeat: no-repeat; background-size: cover;">
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
                                    <label for="email"
                                        class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}
                                        <span style="color:crimson"> *</span></label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input id="email" type="email" autocomplete="off"
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
                                    <label for="password"
                                        class="col-md-4 col-form-label text-md-end">{{ __('Password') }}<span
                                            style="color:crimson"> *</span></label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input id="password" type="password" autocomplete="off"
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
                                                <div class="captcha">
                                                    <span>{!! captcha_img('flat') !!}</span>

                                                    <input type="button" class="btn btn-warning reload" id="reload_1"
                                                        value=&#x21bb;>
                                                </div>
                                            </fieldset>
                                        </div>

                                        <div class="col-sm-6 col-12 float-sm-left text-center text-sm-right"><a
                                                href="{{ route('forgot_password') }}" class="card-link">Forgot Password?</a>
                                        </div>
                                    </div>
                                    <label for="email"
                                        class="col-md-4 col-form-label text-md-end">Captcha
                                        <span style="color:crimson"> *</span></label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input id="captcha" type="captcha" autocomplete="off"
                                            class="form-control @error('captcha') is-invalid @enderror" name="captcha"
                                            value="{{ old('captcha') }}" required autocomplete="captcha" autofocus>
                                        <div class="form-control-position" style="top:15px !important;">
                                            <i class="fa fa-refresh"></i>
                                        </div>
                                    </fieldset>
                                    <button type="submit" class="btn btn-outline-primary btn-block"><i
                                            class="feather icon-unlock"></i> {{ __('Login') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </body>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {

            $("#reload_1").on('click', function() {
                $.ajax({
                    type: "GET",
                    url: "{{ route('reload_captcha') }}",
                    success: function(data) {
                        $(".captcha span").html(data.captcha);
                    }
                });
            });
        });
    </script>
@endsection
