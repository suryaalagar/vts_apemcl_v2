@extends('layouts.alter_app')
@section('content')
    <section class="">
        <div class="row">
            <div class="col-md-12 ">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Change Password</h4> <a class="heading-elements-toggle"><i
                                class="fa fa-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-action="close"><i class="feather icon-x"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form-horizontal form-simple" id="simform">
                                @csrf
                                <div class="row">
                                    <div class="col-xl-12 col-lg-12 col-md-12 mb-1">
                                        <h5 class="text-bold-600">Change Password</h4>
                                    </div>

                                    {{-- <div class="col-xl-4 col-lg-6 col-md-12 mb-1">
                                        <fieldset class="form-group">
                                            <label for="vehicle_name" class="required">Email Id:<span
                                                    class="error">&nbsp;*</span></label>
                                            <input type="text" class="form-control" name="email" id="email"
                                                placeholder="Enter Registered Mail Id">
                                            <div class="div2" id="div2"></div>
                                            <span class="error_msg text-danger"></span>
                                        </fieldset>
                                    </div> --}}
                                    <div class="col-xl-3 col-lg-6 col-md-12 mb-1">
                                        <fieldset class="form-group">
                                            <label for="vehicle_name" class="required">Whatsapp No:<span
                                                    class="error">&nbsp;*</span></label>
                                            <input type="text" class="form-control" name="whatsapp_no" id="whatsapp_no"
                                                placeholder="Enter Registered Whatsapp Number">
                                            <div class="div2" id="div2"></div>
                                            <span class="error_msg text-danger"></span>
                                        </fieldset>
                                    </div>
                                    {{-- <div class="col-xl-4 col-lg-6 col-md-12 mb-1">
                                        <fieldset class="form-group">
                                            <label for="deviceimei" class="required">New Password<span
                                                    class="error">&nbsp;*</span></label>
                                            <input type="text" class="form-control" name="new_password" id="new_password"
                                                placeholder="Enter New Password">
                                            <div class="div2" id="div2"></div>
                                            <span class="error_msg text-danger"></span>
                                        </fieldset>
                                    </div> --}}

                                    <div class="col-xl-3 col-lg-6 col-md-12">
                                        <br>
                                        <input type="submit"
                                            class="btn btn-success btn-min-width mr-1 btn-next btn-next1 block-page"
                                            value="Submit" id='submit'>
                                        <button type="button" class="btn btn-warning btn-min-width"
                                            id="closeform">Reset</button>
                                    </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $("#simform").submit(function(e) {
            e.preventDefault();
            // var simid = $('#simid').val();
            var form = $(this);

            $.ajax({
                type: "POST",
                url: "{{ route('change_password') }}",
                data: form.serialize(), // serializes the form's elements.
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $('#submit').html("Please wait");
                },
                success: function(data) {
                    if (data.success == true) {
                        toastr.success("Data Updated Successfully!", "INSERT", {
                            progressBar: !0
                        });
                        reload();
                    } else {
                        toastr.warning(data.message, "Decline", {
                            progressBar: !0
                        });
                    }
                }
            });


        });
    </script>
@endpush
