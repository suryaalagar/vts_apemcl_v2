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

                                        <div class="table-responsive">
                                            <div class="col-12 table-responsive">
                                                {{-- <div>
                                                <span>Address</span>
                                                <label class="toggle-switch">
                                                    <input type="checkbox" id="myCheck1">
                                                    <span class="slider"></span>
                                                </label>
                                            </div> --}}
                                                <br />
                                                <h3 align="center">Trip Plan Report</h3>

                                                <br />
                                                <table class="table table-striped table-bordered" id="datatable">
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
                                                            <th>Total K.M</th>
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
            $("#selectdate1,#selectdate2,#myCheck1").change(function() {
                get_report();
            });


            function get_report() {

                var fromdate = $('#selectdate1').val();
                var todate = $('#selectdate2').val();
                var active_only = $('#myCheck1').is(':checked') ? 1 : 0;
                var dt = $('#datatable').DataTable({
                    processing: true,
                    serverSide: true,
                    destroy: true,
                    ajax: {
                        url: "{{ route('tripplanreport.complete_report_getData') }}",
                        type: "GET",
                        data: {
                            _token: "{{ csrf_token() }}",
                            fromdate: fromdate,
                            todate: todate,
                            active: active_only
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

                    ],
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel'
                    ]

                });
            }
        });
    </script>

    <script type="text/javascript">
        var marker;
        var markers = [];
        var map = L.map('map').setView([10.84125, 79.84266000000001], 6);
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
        // var Google_layer = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        //     maxZoom: 20,
        //     subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        // });
        var map_count = 0;
        // map.addLayer(Google_layer);
        var fg = L.featureGroup().addTo(map);
        var circleLayerGroup = L.layerGroup().addTo(map);
        var assetLayerGroup = new L.LayerGroup();
        var first_time = true;
        var mapIntervals = [];

        function test_function(trip_id, start_time, end_time, device_imei) {
            console.log("first " + device_imei)
            $("#myModal").modal("show");
            setTimeout(function() {
                map.invalidateSize();
                showMap(trip_id, start_time, end_time);
                park_marker(start_time, end_time, device_imei);
            }, 200);

            // var mapInterval = setInterval(() => {
            //     showMap(trip_id, start_time, end_time);

            //     map_count++;
            // }, 10000);

            // mapIntervals.push(mapInterval);
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
                    // console.log(res);
                    if (fg) {
                        fg.clearLayers();
                        // fg.clearCircles();
                    }
                    var tripplan = res.tripplan;
                    // console.log(tripplan);
                    var playbackData = res.playback;
                    var latLngs = [];
                    for (var i = 0; i < playbackData.length; i++) {
                        latLngs.push(L.latLng(playbackData[i].latitude, playbackData[i].longitude));
                    }

                    var polyline = L.polyline(latLngs, {
                        color: 'blue'
                    }).addTo(fg);

                    var image_path = "{{ asset('assets/dist/img/starts.png') }}";
                    var image_path1 = "{{ asset('assets/dist/img/finish.png') }}";

                    var startm = [playbackData[0].latitude, playbackData[0].longitude];


                    var redIcon = new L.Icon({
                        iconUrl: image_path,
                        iconSize: [65, 65],
                        className: 'starticons',
                    });

                    var redIcon1 = new L.Icon({
                        iconUrl: image_path1,
                        iconSize: [65, 65],
                    });

                    var startmarker = L.marker(startm, {
                        icon: redIcon
                    }).addTo(fg);
                    startmarker.bindPopup('Start Location');

                    var endm = [playbackData[playbackData.length - 1].latitude, playbackData[playbackData
                        .length - 1].longitude];
                    var endmarker = L.marker(endm, {
                        icon: redIcon1
                    }).addTo(fg);
                    endmarker.bindPopup('End Location');


                    for (i = 0; i < tripplan.length; i++) {
                        // alert(res[i].s_lat);

                        start_fence = L.circle([tripplan[i].s_lat, tripplan[i].s_lng], 500).addTo(fg);
                        start_fence.bindPopup('Start Location');
                        end_fence = L.circle([tripplan[i].e_lat, tripplan[i].e_lng], 500).addTo(fg);
                        end_fence.bindPopup('End Location');


                        var encodedPolyline = tripplan[i].polyline; // Replace with your encoded polyline data
                        var planned_polyline = L.Polyline.fromEncoded(encodedPolyline, {
                            color: 'green'
                        }).addTo(fg);

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

                        map.fitBounds(planned_polyline.getBounds());

                    }
                }
            });

        }

        function park_marker(start_time, end_time, device_imei) {
            var parkmarkerss = [];
            console.log("come " + device_imei);
            console.log("come1 " + start_time);
            console.log("come2 " + end_time);
            $.ajax({
                url: '{{ route('tripplanreport.get_parking') }}',
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    device_imei: device_imei,
                    from_date: start_time,
                    to_date: end_time,
                },
                success: function(data) {
                    
                    var parkdata = data;
                    console.log(parkdata);



                    for (var i = 0; i < parkdata.length; i++) {
                        //console.log(parkdata[i].s_lat);
                        var smallIcon = new L.Icon({
                            iconSize: [40, 40],
                            iconAnchor: [13, 27],
                            popupAnchor: [1, -24],
                            iconUrl: "{{ asset('assets/dist/img/truk-icon.png') }}"
                        });
                        var park_duration = parkdata[i].time_difference;
                        var vehicle_name = parkdata[i].id;
                        const popupContent =
                            '<div class="marker">' + "<p>Duration(H:M:S): <b>" + park_duration + "</b></p>"
                        "</div>";

                        parkmarker = L.marker([parkdata[i].start_latitude, parkdata[i].start_longitude], {
                            icon: smallIcon
                        }).addTo(fg);

                        map.addLayer(parkmarker);


                        parkmarker.bindPopup(popupContent);

                        parkmarkerss.push(parkmarker);





                    }
                    assetLayerGroup.addLayer(parkmarkerss);




                }
            });

        }
    </script>
@endpush
