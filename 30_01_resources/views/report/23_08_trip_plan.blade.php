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
    @push('styles')
        <style>
            #map {
                height: 100%;
            }
        </style>
    @endpush

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
        <section id="form-repeater">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-content collapse show box-bordershado">
                            <div class="card-body">
                                <ul class="nav nav-tabs nav-underline" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-value="1" id="baseIcon-tab21" data-toggle="tab"
                                            aria-controls="tabIcon21" href="#tabIcon21" role="tab"
                                            aria-selected="true"><i class="fa fa-play"></i>All</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-value="2" id="baseIcon-tab24" data-toggle="tab"
                                            aria-controls="tabIcon24" href="#tabIcon24" role="tab"
                                            aria-selected="false"><i class="fa fa-flag"></i>In HUB</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-value="3" id="baseIcon-tab22" data-toggle="tab"
                                            aria-controls="tabIcon22" href="#tabIcon22" role="tab"
                                            aria-selected="false"><i class="fa fa-flag"></i>Trip Processing</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-value="4" id="baseIcon-tab23" data-toggle="tab"
                                            aria-controls="tabIcon23" href="#tabIcon23" role="tab"
                                            aria-selected="false"><i class="fa fa-cog"></i>Trip Completed</a>
                                    </li>
                                </ul>
                                <div class="card">
                                    <div class="card-header">
                                    </div>
                                    <div class="tab-content px-1 pt-1">
                                        <div class="tab-pane active" id="tabIcon21" role="tabpanel"
                                            aria-labelledby="baseIcon-tab21">
                                            <div class="card-content collapse show">
                                                <div class="card-body card-dashboard">
                                                    <table class="table table-striped display nowrap table-bordered"
                                                        id="datatable_all" style="width:100%">
                                                        <thead class="bg-primary">
                                                            <tr>
                                                                <th>S.No</th>
                                                                <th>Trip id</th>
                                                                <th>POC Number</th>
                                                                <th>IMEI/ SIM</th>
                                                                <th>TripDate</th>
                                                                <th>Vehicle Name</th>
                                                                <th>Route Name</th>
                                                                <th>Map View</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="ln_solid"></div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tabIcon24" role="tabpanel"
                                            aria-labelledby="baseIcon-tab24">
                                            <div class="card-content collapse show">
                                                <div class="card-body card-dashboard">
                                                    <table class="table table-striped display nowrap table-bordered"
                                                        id="datatable_inhub" style="width:100%">
                                                        <thead class="bg-primary">
                                                            <tr>
                                                                <th>S.No</th>
                                                                <th>Trip id</th>
                                                                <th>POC Number</th>
                                                                <th>IMEI/SIM</th>
                                                                <th>TripDate</th>
                                                                <th>Vehicle Name</th>
                                                                <th>Route Name</th>
                                                                <th>Map View</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="ln_solid"></div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tabIcon22" role="tabpanel"
                                            aria-labelledby="baseIcon-tab22">
                                            <div class="card-content collapse show">
                                                <div class="card-body card-dashboard">
                                                    <table class="table table-striped display nowrap table-bordered"
                                                        id="datatable_processing" style="width:100%">
                                                        <thead class="bg-primary">
                                                            <tr>
                                                                <th>S.No</th>
                                                                <th>Trip id</th>
                                                                <th>POC Number</th>
                                                                <th>IMEI/SIM</th>
                                                                <th>TripDate</th>
                                                                <th>Vehicle Name</th>
                                                                <th>Route Name</th>
                                                                <th>Map View</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="ln_solid"></div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tabIcon23" role="tabpanel"
                                            aria-labelledby="baseIcon-tab23">
                                            <div class="card-content collapse show">
                                                <div class="card-body card-dashboard">
                                                    <table class="table table-striped display nowrap table-bordered"
                                                        id="datatable_complete" style="width:100%">
                                                        <thead class="bg-primary">
                                                            <tr>
                                                                <th>S.No</th>
                                                                <th>Trip id</th>
                                                                <th>POC Number</th>
                                                                <th>Device IMEI</th>
                                                                <th>TripDate</th>
                                                                <th>Vehicle Name</th>
                                                                <th>Route Name</th>
                                                                <th>Start Odometer</th>
                                                                <th>End Odometer</th>
                                                                <th>Start Time</th>
                                                                <th>End Time</th>
                                                                <th>Map View</th>
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
                        </div>
                    </div>
                </div>
            </div>
        </section>
        {{-- <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="map" id="map">
                                <div style="width: 100px; height: 500px;" id="map_canvas"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">Trip Single Data</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body modal_offset">
                        <div class="row">
                            <div class="col-md-12 modal_body_content">
                                <p>Location : Karnataka</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 modal_body_map">
                                <div class="map" id="map">
                                    <div style="width: 100px; height: 400px;" id="map_canvas"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 modal_body_end">
                                <p>APEMCL</p>
                            </div>
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
                    var column_data = [{
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
                        {
                            data: 'Action',
                            name: 'Action'
                        }
                    ];
                }
                if (status == 'In HUB') {
                    var trip_status = 1;
                    var datatable_id = '#datatable_inhub';
                    var column_data = [{
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
                        {
                            data: 'Action',
                            name: 'Action'
                        }
                    ];
                }
                if (status == 'Trip Processing') {
                    var trip_status = 2;
                    var datatable_id = '#datatable_processing';
                    var column_data = [{
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
                        {
                            data: 'Action',
                            name: 'Action'
                        }
                    ];
                }
                if (status == 'Trip Completed') {
                    var trip_status = 3;
                    var datatable_id = '#datatable_complete';
                    var column_data = [{
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
                        {
                            data: 'start_odometer',
                            name: 'start_odometer'
                        },
                        {
                            data: 'end_odometer',
                            name: 'end_odometer'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                        {
                            data: 'updated_at',
                            name: 'updated_at'
                        },
                        {
                            data: 'Action',
                            name: 'Action'
                        }
                    ];
                }

                $.fn.dataTable.ext.errMode = 'throw';
                $(datatable_id).DataTable({

                    processing: true,
                    serverSide: true,
                    scrollX: true,
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
                    columns: column_data
                });


            });


        });
    </script>

    <script type="text/javascript">
        var marker;
        var markers = [];
        var map = L.map('map').setView([10.84125, 79.84266000000001], 6);
        // create a new tile layer
        var tileUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            layer = new L.TileLayer(tileUrl, {
                attribution: 'Maps © <a href=\"www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors',
                maxZoom: 15,
                noWrap: true,
            });
        // L.control.zoom({
        //     position: 'topright'
        // }).addTo(map);
        // add the layer to the map
        // Google Layer
        var Google_layer = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        });
        map.addLayer(Google_layer);
        var fg = L.featureGroup().addTo(map);
        var circleLayerGroup = L.layerGroup().addTo(map);
        var assetLayerGroup = new L.LayerGroup();
        var first_time = true;

        function test_function(trip_id) {
            $("#myModal").modal("show");
            setTimeout(function() {
                map.invalidateSize();
                showMap(trip_id);
            }, 200);

            setInterval(() => {
                showMap(trip_id);
            }, 10000);
        }



        function showMap(trip_id) {
            $.ajax({
                url: '{{ route('trip_plan.planned_trips') }}',
                type: 'GET',
                dataType: 'json',
                data: {
                    trip_id: trip_id // Add your data here
                },
                success: function(res) {
                    if (fg) {
                        fg.clearLayers();
                        // fg.clearCircles();
                    }
                    var start_fence;
                    var start_fence = [];
                    var end_fence;
                    var end_fence = [];
                    for (i = 0; i < res.length; i++) {
                        // alert(res[i].s_lat);
                        start_fence = L.circle([res[i].s_lat, res[i].s_lng], 500).addTo(map);
                        start_fence.bindPopup('Start Location');
                        end_fence = L.circle([res[i].e_lat, res[i].e_lng], 500).addTo(map);
                        end_fence.bindPopup('End Location');
                     
                        var encodedPolyline = res[i].polyline; // Replace with your encoded polyline data
                        var polyline = L.Polyline.fromEncoded(encodedPolyline, {
                            color: 'blue',
                            weight: 2,
                            opacity: 0.7
                        }).addTo(map);

                        if (res[i].update_time <= 10) {
                            if (res[i].ignition == 1) {
                                if (res[i].speed > 0) {

                                    var image_path = "{{ asset('assets/dist/img/ICONS/GREEN/truck.png') }}";

                                } else {
                                    var image_path = "{{ asset('assets/dist/img/ICONS/YELLOW/truck.png') }}";
                                }
                            } else {
                                var image_path = "{{ asset('assets/dist/img/ICONS/BLUE/truck.png') }}";
                            }
                        } else {
                            var image_path = "{{ asset('assets/dist/img/ICONS/GRAY/truck.png') }}";
                        }
                        var speed = res[i].speed == null ? 0 : res[i].speed;
                        var redIcon = new L.Icon({
                            iconUrl: image_path,
                            iconSize: [40, 40],
                        });

                        var angle = res[i].angle;

                        marker = new L.marker([res[i].lattitute, res[i].longitute], {
                                icon: redIcon,
                                rotationAngle: angle
                            })
                            .addTo(fg);
                        map.addLayer(marker);

                        markers.push(marker);
                        assetLayerGroup.addLayer(marker);

                        clearcontent(start_fence, end_fence);

                    }
                }
            });
        }

        function clearcontent() {
            start_fence.removeFrom(map);
            end_fence.removeFrom(map);
        }
    </script>
@endpush
