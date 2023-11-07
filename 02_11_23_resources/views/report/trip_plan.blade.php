@extends('layouts.app')
@section('content')

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
                                            <form action="{{ route('tripplan.export') }}" method="GET">
                                                @csrf
                                                <input type="hidden" id="export_from_date" name="fromDate">
                                                <input type="hidden" id="export_to_date" name="toDate">
                                            </form>
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
                                                        <th>Start Time</th>
                                                        <th>End Time</th>
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
    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Trip Plan Report</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body modal_offset">
                    <div class="row">
                        <div class="col-md-12 modal_body_content">
                            <input type="checkbox" id="parkview">Park View
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $("#selectdate1,#selectdate2,#myCheck1").change(function() {
                get_report();
            });


            function get_report() {

                const export_from_date = document.getElementById('export_from_date');
                const export_to_date = document.getElementById('export_to_date');
                export_from_date.value =  $('#selectdate1').val();
                export_to_date.value =  $('#selectdate2').val();
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

                    ],
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel'
                    ]

                });
            }

            // $('#export-button').on('click', function () {
            //     var fromdate = $('#selectdate1').val();
            //     var todate = $('#selectdate2').val();
               
            //     $.ajax({
            //         url: "{{ route('tripplan.export') }}",
            //         type: "GET",
            //         data: {
            //             _token: "{{ csrf_token() }}",
            //             fromdate: fromdate,
            //             todate: todate
            //         },
            //         success: function () {
            //             console.log("success");
            //         }
            //     });
            // });
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
        var map_count = 0;
        map.addLayer(Google_layer);
        var fg = L.featureGroup().addTo(map);
        // polyline = L.polyline(mypolyline).addTo(active_polyline);
        var circleLayerGroup = L.layerGroup().addTo(map);
        var assetLayerGroup = new L.LayerGroup();
        var first_time = true;
        var mapIntervals = [];

        function test_function(trip_id, start_time, end_time, device_imei) {
            $("#myModal").modal("show");
            setTimeout(function() {
                map.invalidateSize();
                showMap(trip_id, start_time, end_time);
                park_marker(start_time, end_time,device_imei);
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
                    // console.log(playbackData);
                    var start_fence;
                    var start_fence = [];
                    var end_fence;
                    var end_fence = [];
                    var latLngs = [];
                    for (var i = 0; i < playbackData.length; i++) {
                        latLngs.push(L.latLng(playbackData[i].latitude, playbackData[i].longitude));
                    }

                    var polyline = L.polyline(latLngs, {
                        color: 'blue'
                    }).addTo(fg);

                    for (i = 0; i < tripplan.length; i++) {
                        if (map_count == 0) {
                            start_fence = L.circle([tripplan[i].s_lat, tripplan[i].s_lng], 500).addTo(fg);
                            start_fence.bindPopup('Start Location');
                            end_fence = L.circle([tripplan[i].e_lat, tripplan[i].e_lng], 500).addTo(fg);
                            end_fence.bindPopup('End Location');
                        }
                        var encodedPolyline = tripplan[i].polyline; // Replace with your encoded polyline data
                        var planned_polyline = L.Polyline.fromEncoded(encodedPolyline, {
                            color: 'blue',
                            weight: 2,
                            opacity: 0.7
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
                        // clearcontent(start_fence, end_fence);
                    }
                }
            });
        }

        // $('#parkview').click(function() {

        // if ($("input[type=checkbox]").is(":checked")) {
        //     park_marker(vehicle_name);
        // } else {

        //     for (var i = 0; i < parkmarkerss.length; i++) {

        //         map.removeLayer(parkmarkerss[i]);
        //     }
        //     for (var i = 0; i < speedmarkerss.length; i++) {

        //         map.removeLayer(speedmarkerss[i]);
        //     }
        // }
        // });

        function park_marker(start_time, end_time,device_imei) {
            var parkmarkerss = [];
            console.log("come " + device_imei);
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
                            iconUrl: "{{ 'assets/dist/img/truk-icon.png' }}"
                        });
                        var park_duration = parkdata[i].time_difference;
                        var vehicle_name = parkdata[i].id;
                        const popupContent =
                            '<div class="marker">' + "<p>Vehicle No: <b>" + vehicle_name + "</b></p>" + "<p>Duration(H:M:S): <b>" + park_duration + "</b></p>"
                        "</div>";
                        
                        parkmarker = L.marker([parkdata[i].start_latitude, parkdata[i].start_longitude], {
                            icon: smallIcon
                        }).addTo(map);

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
