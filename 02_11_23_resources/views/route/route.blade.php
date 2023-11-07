@extends('layouts.app')
@section('content')
    <section class="">
        <div class="row">
            <div class="col-md-12 adduser" style="display:none">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Add Vehicle </h4> <a class="heading-elements-toggle"><i
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
                                <div class="row">
                                    <div class="col-xl-12 col-lg-12 col-md-12 mb-1">
                                        <h5 class="text-bold-600">Vehicle</h4>
                                    </div>

                                    <div class="col-xl-4 col-lg-6 col-md-12 mb-1">
                                        <fieldset class="form-group">
                                            <label for="vehicle_name" class="required">Vehicle Number<span
                                                    class="error">&nbsp;*</span></label>
                                            <input type="text" class="form-control" name="vehicle_name" id="vehicle_name"
                                                placeholder="Enter the Vehicle Number">
                                            <div class="div2" id="div2"></div>
                                            <span class="error_msg text-danger"></span>
                                        </fieldset>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-md-12 mb-1">
                                        <fieldset class="form-group">
                                            <label for="deviceimei" class="required">Device Imei<span
                                                    class="error">&nbsp;*</span></label>
                                            <input type="text" class="form-control" name="device_imei" id="device_imei"
                                                placeholder="Enter the IMEI Number">
                                            <div class="div2" id="div2"></div>
                                            <span class="error_msg text-danger"></span>
                                        </fieldset>
                                    </div>

                                    <div class="col-xl-4 col-lg-6 col-md-12 mb-1">
                                        <fieldset class="form-group">
                                            <label for="imeinumber" class="required">Sim
                                                Number<span class="error">&nbsp;*</span></label>
                                            <input type="text" class="form-control" name="sim_mob_no" id="sim_mob_no"
                                                placeholder="Enter the IMEI Number">
                                            <div class="div2" id="div2"></div>
                                            <span class="error_msg text-danger"></span>
                                        </fieldset>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-md-12">
                                        <fieldset class="form-group">
                                            <label for="gender">Status</label>
                                            <select class="form-control" id="status" name="status">

                                                <option value="1">Active</option>
                                                <option value="2">Deactive</option>
                                            </select>
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

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif



    <section id="configuration">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                    </div>
                    <div class="card-content collapse show">
                        <div class="row">
                            <div class="col-12">
                                <div class="card-body card-dashboard">
                                    <div class="content-header-right col-md-12 col-12 mb-md-0 mb-2">
                                        <div class="btn-group float-md-right" role="group"
                                            aria-label="Button group with nested dropdown">
                                            <div class="btn-group role="group">
                                                <a href="{{ route('route.route_create') }}"><button
                                                        class="btn btn-outline-primary" type="button"> <i
                                                            class="feather icon-user-plus icon-left"></i> Add Vehicle
                                                    </button></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <div class="col-12 table-responsive">
                                            <br />
                                            <h3 align="center">Routes</h3>

                                            <br />
                                            <table class="table table-striped table-bordered" id="datatable">
                                                <thead class="bg-primary">
                                                    <tr>
                                                        <th>S.No</th>
                                                        <th>Route Name</th>
                                                        <th>Route Start Name</th>
                                                        <th>Route Start Lat,Lng</th>
                                                        <th>Route End Name</th>
                                                        <th>Route End Lat,Lng</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
    </section>
    <!-- Modal -->


    @push('scripts')
        <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#datatable').DataTable({

                    processing: true,
                    serverSide: true,
                    method: 'GET',
                    ajax: "{{ route('route.getData') }}",
                    columns: [{
                            data: 'S No',
                            name: 'S No'
                        },
                        {
                            data: 'routename',
                            name: 'routename'
                        },
                        {
                            data: 'routename',
                            name: 'routename'
                        },
                        {
                            data: 'sim_mob_no',
                            name: 'sim_mob_no'
                        },
                        {
                            data: 'Action',
                            name: 'Action'
                        },
                    ]

                });
            });
            $("#showuser").click(function() {
                $('#simform')[0].reset();
                $('#simid').val("");

                $('.adduser').show(2000); //Add userpage hide
                $('#configuration').hide(); // hide view page
            });
            $("#closeform").click(function() {
                location.reload();
            });
        </script>
    @endpush
@endsection
