{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dattable Example by PHP TECH LIFE</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.dataTables.css') }}"/>
    <script type="text/javascript" src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/jquery.dataTables.js') }}"></script>    
</head> --}}
@extends('layouts.app')
@section('content')

    <body>
        <section id="form-repeater">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-content collapse show box-bordershado">
                            <div class="card-body">
                                <div class="repeater-default">
                                    <div data-repeater-list="car">
                                        <div data-repeater-item>
                                            <form class="form row" method="post" action="">
                                                @csrf
                                                <div class="form-group mb-1 col-sm-12 col-md-3">
                                                    <label for="pass">From Date</label>
                                                    <br>
                                                    <input type="datetime-local" class="form-control" id="selectdate1"
                                                        name="fromdate" value='{{ $from_date }}'>
                                                </div>
                                                <div class="form-group mb-1 col-sm-12 col-md-3">
                                                    <label for="pass">To Date</label>
                                                    <br>
                                                    <input type="datetime-local" class="form-control" id="selectdate2"
                                                        name="todate" value='{{ $to_date }}'>
                                                </div>




                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="card-body">
            <ul class="nav nav-tabs nav-underline" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-value="1" id="baseIcon-tab21" data-toggle="tab"
                        aria-controls="tabIcon21" href="#tabIcon21" role="tab" aria-selected="true"><i
                            class="fa fa-play"></i>All</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-value="2" id="baseIcon-tab24" data-toggle="tab" aria-controls="tabIcon24"
                        href="#tabIcon24" role="tab" aria-selected="false"><i class="fa fa-flag"></i>In HUB</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-value="3" id="baseIcon-tab22" data-toggle="tab" aria-controls="tabIcon22"
                        href="#tabIcon22" role="tab" aria-selected="false"><i class="fa fa-flag"></i>Trip Processing</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-value="4" id="baseIcon-tab23" data-toggle="tab" aria-controls="tabIcon23"
                        href="#tabIcon23" role="tab" aria-selected="false"><i class="fa fa-cog"></i>Trip Completed</a>
                </li>
            </ul>
            <div class="card">
                <div class="card-header">
                </div>
                <div class="tab-content px-1 pt-1">
                    <div class="tab-pane active" id="tabIcon21" role="tabpanel" aria-labelledby="baseIcon-tab21">
                        <div class="card-content collapse show">
                            <div class="card-body card-dashboard">
                                <table class="table table-striped table-bordered" id="datatable_all">
                                    <thead class="bg-primary">
                                        <tr>
                                            <th>S.No</th>
                                            <th>Trip id</th>
                                            <th>POC Number</th>
                                            <th>IMEI/ SIM</th>
                                            <th>TripDate</th>
                                            <th>Vehicle Name</th>
                                            <th>Route Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div class="ln_solid"></div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tabIcon24" role="tabpanel" aria-labelledby="baseIcon-tab24">
                        <div class="card-content collapse show">
                            <div class="card-body card-dashboard">
                                <table class="table table-striped table-bordered" id="datatable_inhub">
                                    <thead class="bg-primary">
                                        <tr>
                                            <th>S.No</th>
                                            <th>Trip id</th>
                                            <th>POC Number</th>
                                            <th>IMEI/SIM</th>
                                            <th>TripDate</th>
                                            <th>Vehicle Name</th>
                                            <th>Route Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div class="ln_solid"></div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tabIcon22" role="tabpanel" aria-labelledby="baseIcon-tab22">
                        <div class="card-content collapse show">
                            <div class="card-body card-dashboard">
                                <table class="table table-striped table-bordered" id="datatable_processing">
                                    <thead class="bg-primary">
                                        <tr>
                                            <th>S.No</th>
                                            <th>Trip id</th>
                                            <th>POC Number</th>
                                            <th>IMEI/SIM</th>
                                            <th>TripDate</th>
                                            <th>Vehicle Name</th>
                                            <th>Route Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div class="ln_solid"></div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tabIcon23" role="tabpanel" aria-labelledby="baseIcon-tab23">
                        <div class="card-content collapse show">
                            <div class="card-body card-dashboard">
                                <table class="table table-striped table-bordered" id="datatable_complete">
                                    <thead class="bg-primary">
                                        <tr>
                                            <th>S.No</th>
                                            <th>Trip id</th>
                                            <th>POC Number</th>
                                            <th>Device IMEI</th>
                                            <th>TripDate</th>
                                            <th>Vehicle Name</th>
                                            <th>Route Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div class="ln_solid"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </body>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $(".nav-link").on('click', function() {
                console.log("SUCCESS");
                var status = $(this).text();

                var fromdate = $('#selectdate1').val();
                var todate = $('#selectdate2').val();

                if (status == 'All') {
                    var trip_status = 0;
                    var datatable_id = '#datatable_all';
                }
                if (status == 'In HUB') {
                    var trip_status = 1;
                    var datatable_id = '#datatable_inhub';
                }
                if (status == 'Trip Processing') {
                    var trip_status = 2;
                    var datatable_id = '#datatable_processing';
                }
                if (status == 'Trip Completed') {
                    var trip_status = 3;
                    var datatable_id = '#datatable_complete';
                }
                console.log(fromdate);
                console.log(todate);
                console.log(trip_status);

                $.fn.dataTable.ext.errMode = 'throw';
                $(datatable_id).DataTable({

                    processing: true,
                    serverSide: true,
                    responsive: true,
                    "bDestroy": true,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    ajax: {
                        url: "{{ route('trip_plan.getData') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            fromdate: fromdate,
                            todate: todate,
                            trip_status: trip_status
                        }
                    },
                    columns: [{
                            data: 'S No',
                            name: 'S No'
                        },
                        {
                            data: 'trip_id',
                            name: 'trip_id'
                        },
                        {
                            data: 'poc_number',
                            name: 'poc_number'
                        },
                        {
                            data: 'device_no',
                            name: 'device_no'
                        },
                        {
                            data: 'trip_date',
                            name: 'trip_date'
                        },
                        {
                            data: 'vehicle_name',
                            name: 'vehicle_name'
                        },
                        {
                            data: 'route_name',
                            name: 'route_name'
                        },
                    ]

                });


            });


        });

        // $(function() {
        //     $("#alltrips,#inhubtrips,#completetrips,#processtrips").dataTable({
        //         scrollX: true,
        //         dom: 'Bfrtip',
        //         buttons: [
        //             'excel'
        //         ]
        //     });
        // })
    </script>
@endpush
