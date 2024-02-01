{{-- @extends('layouts.app') --}}
@section('content')
    <section class="">
        <div class="row">
            <div class="col-md-12 ">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Forgot Password</h4> <a class="heading-elements-toggle"><i
                                class="fa fa-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-action="close"><i class="feather icon-x"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form-horizontal form-simple" id="simform" method="post" novalidate
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-xl-12 col-lg-12 col-md-12 mb-1">
                                        <h5 class="text-bold-600">Forgot Password</h4>
                                    </div>

                                    <div class="col-xl-4 col-lg-6 col-md-12 mb-1">
                                        <fieldset class="form-group">
                                            <label for="vehicle_name" class="required">Email Id:<span
                                                    class="error">&nbsp;*</span></label>
                                            <input type="text" class="form-control" name="email" id="email"
                                                placeholder="Enter Registered Mail Id">
                                            <div class="div2" id="div2"></div>
                                            <span class="error_msg text-danger"></span>
                                        </fieldset>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-md-12 mb-1">
                                        <fieldset class="form-group">
                                            <label for="deviceimei" class="required">New Password<span
                                                    class="error">&nbsp;*</span></label>
                                            <input type="text" class="form-control" name="password" id="password"
                                                placeholder="Enter New Password">
                                            <div class="div2" id="div2"></div>
                                            <span class="error_msg text-danger"></span>
                                        </fieldset>
                                    </div>

                                    <input type="hidden" name="id" id="id" value="">
                                    <input type="hidden" name="vehicle_type_id" id="vehicle_type_id" value="1">

                                    <div class="col-xl-12 col-lg-12 col-md-12">
                                        <input type="submit"
                                            class="btn btn-primary btn-min-width mr-1 btn-next btn-next1 block-page"
                                            value="Submit" id='submit'>
                                        <button type="button" class="btn btn-primary btn-min-width"
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
            alert("hello");
            var simid = $('#simid').val();
            e.preventDefault();
            var form = $(this);

            $.ajax({
                type: "POST",
                url: "{{ route('vehicle.store') }}",
                data: form.serialize(), // serializes the form's elements.
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {

                    if (data.message == 'Success') {
                        toastr.success("Data Updated Successfully!", "INSERT", {
                            progressBar: !0
                        });
                        window.location.href = '{{ route('vehicle.index') }}';
                    }
                    if (data.message == 'validaton_error') {
                        toastr.warning('Validate Error!', "Decline", {
                            progressBar: !0
                        });
                    } else {
                        toastr.warning("Data Not Inserted!", "Decline", {
                            progressBar: !0
                        });
                    }
                }
            });


        });
    </script>
@endpush
