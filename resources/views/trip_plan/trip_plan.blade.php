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

            .legend {
                padding: 6px 8px;
                font: 14px Arial, Helvetica, sans-serif;
                background: white;
                background: rgba(255, 255, 255, 0.8);
                /*box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);*/
                /*border-radius: 5px;*/
                line-height: 24px;
                color: #555;
            }

            .legend h4 {
                text-align: center;
                font-size: 16px;
                margin: 2px 12px 8px;
                color: #777;
            }

            .legend span {
                position: relative;
                bottom: 3px;
            }

            .legend i {
                width: 18px;
                height: 18px;
                float: left;
                margin: 0 8px 0 0;
                opacity: 0.7;
            }

            .legend i.icon {
                background-size: 18px;
                background-color: rgba(255, 255, 255, 1);
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
                                <div class="btn-group role=" group">
                                    <a href="{{ route('trip.create') }}"><button class="btn btn-outline-primary"
                                            type="button"> <i class="feather icon-user-plus icon-left"></i>
                                            Add Trip
                                        </button></a>
                                </div>
                                <ul class="nav nav-tabs nav-underline" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-value="1" id="baseIcon-tab21" data-toggle="tab"
                                            aria-controls="tabIcon21" href="#tabIcon21" role="tab" aria-selected="true"
                                            onchange="filterByDate()"><i class="fa fa-play"></i>All</a>
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
                                                                <th>Total KM</th>
                                                                <th>Start Time</th>
                                                                <th>End Time</th>
                                                                <th>Trip Duration</th>
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
                        <button type="button" id="modalCloseButton" class="close" data-dismiss="modal"
                            aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
                // alert("SUCCESS");
                var status = $(this).text();

                var fromdate = $('#selectdate1').val();
                var todate = $('#selectdate2').val();
                console.log(fromdate);
                console.log(todate);
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
                            data: 'total_km',
                            name: 'total_km'
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
                            data: 'time_difference',
                            name: 'time_difference'
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
                        },
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
        // var tileUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        //     layer = new L.TileLayer(tileUrl, {
        //         attribution: 'Maps © <a href=\"www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors',
        //         maxZoom: 15,
        //         noWrap: true,
        //     });
        // L.control.zoom({
        //     position: 'topright'
        // }).addTo(map);
        // add the layer to the map
        // Google Layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 20
        }).addTo(map);

        var legend = L.control({
            position: "bottomleft"
        });
        legend.onAdd = function(map) {
            var div = L.DomUtil.create("div", "legend");
            div.innerHTML += "<h4>Trip Plan</h4>";
            div.innerHTML += '<i style="background:green"></i><span>Planned Path</span><br>';
            div.innerHTML += '<i style="background: #0000FF"></i><span>Actual Path</span><br>';
            return div;
        };

        legend.addTo(map);

        // var Google_layer = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        //     maxZoom: 20,
        //     subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        // });
        var map_count = 0;
        // map.addLayer(Google_layer);
        var fg = L.featureGroup().addTo(map);
        // polyline = L.polyline(mypolyline).addTo(active_polyline);
        var circleLayerGroup = L.layerGroup().addTo(map);
        var assetLayerGroup = new L.LayerGroup();
        var first_time = true;
        var mapIntervals = [];

        function test_function(trip_id, start_time, end_time) {
            $("#myModal").modal("show");
            setTimeout(function() {
                map.invalidateSize();
                showMap(trip_id, start_time, end_time);
            }, 200);

            var mapInterval = setInterval(() => {
                showMap(trip_id, start_time, end_time);
                map_count++;
            }, 10000);

            mapIntervals.push(mapInterval);
        }

        function clearMapIntervals() {
            for (var i = 0; i < mapIntervals.length; i++) {
                clearInterval(mapIntervals[i]);
            }
            mapIntervals = []; // Reset the array
        }

        document.getElementById('modalCloseButton').addEventListener('click', function() {
            closeModal();
        });
        // Function to close the modal
        function closeModal() {
            // Close your modal logic here

            // Clear the map intervals when the modal is closed
            clearMapIntervals();
        }




        function showMap(trip_id, start_time, end_time) {
            $.ajax({
                url: '{{ route('trip_plan.planned_trips') }}',
                type: 'GET',
                dataType: 'json',
                data: {
                    trip_id: trip_id,
                    start_time: start_time,
                    end_time: end_time

                },
                success: function(res) {
                    if (fg) {
                        fg.clearLayers();
                    }
                    var tripplan = res.tripplan;
                    console.log(tripplan);
                    var playbackData = res.playback;
                    var start_fence;
                    var start_fence = [];
                    var end_fence;
                    var end_fence = [];
                    var latLngs = [];
                    if (playbackData) {
                        for (var i = 0; i < playbackData.length; i++) {
                            if (playbackData[i] && playbackData[i].latitude && playbackData[i].longitude) {
                                latLngs.push(L.latLng(playbackData[i].latitude, playbackData[i].longitude));
                            }
                        }
                    }

                    if (latLngs.length > 0) {
                        var polyline = L.polyline(latLngs, {
                            color: 'blue',
                            weight: 4,
                            opacity: 0.9
                        }).addTo(fg);
                    }


                    for (i = 0; i < tripplan.length; i++) {
                        // alert(res[i].s_lat);
                        // if (map_count == 0) {
                        start_fence = L.circle([tripplan[i].s_lat, tripplan[i].s_lng], 500).addTo(fg);
                        start_fence.bindPopup('Start Location');
                        end_fence = L.circle([tripplan[i].e_lat, tripplan[i].e_lng], 500).addTo(fg);
                        end_fence.bindPopup('End Location');
                        // }
                        var encodedPolyline = tripplan[i].polyline;
                        // if(encodedPolyline){
                        var planned_polyline = L.Polyline.fromEncoded(encodedPolyline, {
                            color: 'green',
                            weight: 4,
                            opacity: 0.9
                        }).addTo(fg);
                        // }

                        if (tripplan[i].update_time <= 10) {
                            if (tripplan[i].ignition == 1) {
                                if (tripplan[i].speed > 0) {

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
                        var speed = tripplan[i].speed == null ? 0 : tripplan[i].speed;
                        var redIcon = new L.Icon({
                            iconUrl: image_path,
                            iconSize: [40, 40],
                        });

                        var angle = tripplan[i].angle;

                        marker = new L.marker([tripplan[i].lattitute, tripplan[i].longitute], {
                                icon: redIcon,
                                rotationAngle: angle
                            })
                            .addTo(fg);
                        map.addLayer(marker);

                        markers.push(marker);
                        assetLayerGroup.addLayer(marker);

                        // clearcontent(start_fence, end_fence);

                    }
                }
            });
        }

        // function clearcontent() {
        //     start_fence.removeFrom(map);
        //     end_fence.removeFrom(map);
        // }
    </script>
@endpush
