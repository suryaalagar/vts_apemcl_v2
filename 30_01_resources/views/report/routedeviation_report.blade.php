@extends('layouts.app')
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.1.1/css/buttons.dataTables.min.css">
@push('styles')
    <style>
    </style>
@endpush
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
                                            <br />
                                            <h3 align="center">Route Deviation Report</h3>

                                            <br />
                                            <table class="table table-striped table-bordered " id="datatable">
                                                <thead class="bg-primary">
                                                    <tr>
                                                        <th>S.No</th>
                                                        <th>Vehicle Name</th>
                                                        <th>Route Name</th>
                                                        <th>Route Deviate Out Time</th>
                                                        <th>Route Deviate In Time</th>
                                                        <th>Route Deviate Out Location</th>
                                                        <th>Route Deviate In Location</th>
                                                        <th>Duration</th>
                                                        <th>Map View</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    {{-- @php
                                                        $s_no = 1;
                                                    @endphp
                                                    @foreach ($routedeviation_data as $route_deviate)
                                                        <tr>
                                                            {{-- <td>{{ $trip->client_id }}</td> --}}
                                                    {{-- <td>{{ $trip->vehicleid }}</td> --}}
                                                    {{-- <td>{{ $s_no++ }}</td>
                                                            <td>{{ $route_deviate->vehicle_name }}</td>
                                                            <td>{{ $route_deviate->route_name }}</td>
                                                            <td>{{ $route_deviate->route_deviate_outtime }}</td>
                                                            <td>{{ $route_deviate->route_out_location }}</td>
                                                            <td>{{ $route_deviate->route_deviate_intime }}</td>
                                                            <td>{{ $route_deviate->route_in_location }}</td>
                                                            <td><button type="button" class="btn btn-success showModal"
                                                                    data-toggle="modal" data-target="#myModal"
                                                                    data-planned_route='{{ $polyline_data->planned_route }}'
                                                                    data-actual_running='{{ $polyline_data->actual_running }}'>
                                                                    Map View
                                                                </button></td>
                                                        </tr>
                                                    @endforeach --}}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
    </section>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Route Deviation Report</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body modal_offset">
                    <div class="row">
                        <div class="col-md-12 modal_body_content">
                            <p><b>Route Out Location : <span id="route_out_Location"></span></b></p>
                            <p><b>Route In Location : <span id="route_in_Location"></span></b></p>
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
    <!-- Include jQuery -->
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Include DataTables -->
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script> --}}

    <!-- Include DataTables Buttons -->
    <script src="https://cdn.datatables.net/buttons/2.1.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.1/js/buttons.html5.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $("#selectdate1,#selectdate2,#myCheck1").change(function() {
                get_report();
            });

            // $("#customExcelButton").on("click", function() {
            //     custom_excel_export();
            // });

            // function custom_excel_export() {
            //     var fromdate = $('#selectdate1').val();
            //     var todate = $('#selectdate2').val();
            //     var active_only = $('#myCheck1').is(':checked') ? 1 : 0;

            //     // Perform Ajax request to your controller query for Excel export
            //     $.ajax({
            //         url: "{{ route('routedeviationreport.getData') }}", // Replace with your actual route
            //         type: "POST",
            //         data: {
            //             _token: "{{ csrf_token() }}",
            //             fromdate: fromdate,
            //             todate: todate,
            //             active: active_only
            //         },
            //         success: function(response) {
            //             // Handle success, e.g., display a message or download the file
            //         },
            //         error: function(xhr, status, error) {
            //             // Handle error
            //         }
            //     });
            // }

            function get_report() {

                var fromdate = $('#selectdate1').val();
                var todate = $('#selectdate2').val();
                var active_only = $('#myCheck1').is(':checked') ? 1 : 0;
                $.fn.dataTable.ext.errMode = 'throw';
                var dt = $('#datatable').DataTable({

                    processing: true,
                    serverSide: true,
                    destroy: true,
                    ajax: {
                        url: "{{ route('routedeviationreport.getData') }}",
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
                            data: 'vehicle_name',
                            name: 'vehicle_name'
                        },
                        {
                            data: 'route_name',
                            name: 'route_name'
                        },
                        {
                            data: 'route_deviate_outtime',
                            name: 'route_deviate_outtime'
                        },
                        {
                            data: 'route_deviate_intime',
                            name: 'route_deviate_intime'
                        },
                        {
                            data: 'route_out_address',
                            name: 'route_out_address'
                        },
                        {
                            data: 'route_in_address',
                            name: 'route_in_address'
                        },
                        {
                            data: 'time_difference',
                            name: 'time_difference'
                        },
                        {
                            data: 'Action',
                            name: 'Action'
                        },

                    ],
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel'
                    ]

                });
            }
        });
    </script>
    {{-- <link rel="stylesheet" href="{{ 'https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css' }}" />
    <script src="{{ 'https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js' }}"></script> --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js"></script> --}}
    {{-- <script src="script.js"></script> --}}
    <script type="text/javascript">
        var map = L.map('map').setView([10.84125, 79.84266000000001], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 20
        }).addTo(map);

        // create a new tile layer
        // var tileUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        //     layer = new L.TileLayer(tileUrl, {
        //         attribution: 'Maps © <a href=\"www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors',
        //         maxZoom: 20,
        //         noWrap: true,
        //     });
        // // add the layer to the map
        // // Google Layer
        // var Google_layer = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        //     maxZoom: 20,
        //     subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        // });
        // map.addLayer(Google_layer);
        var fg = L.featureGroup().addTo(map);
        // $('.showModal').on('click', function() {
        //     // console.log(lat);
        //     var planned_route = $(this).data('planned_route');
        //     var actual_running = $(this).data('actual_running');
        //     setTimeout(function() {
        //         map.invalidateSize();

        //         showMap(planned_route, actual_running);
        //     }, 200);
        // });
        function route_deviation_data(start_time, end_time, route_name, route_id) {
            console.log(start_time);
            console.log(end_time);
            $("#myModal").modal("show");
            setTimeout(function() {
                map.invalidateSize();
                showMap(start_time, end_time, route_name, route_id);
            }, 200);
        }

        function showMap(start_time, end_time, route_name, route_id) {
            console.log(start_time);

            $.ajax({
                url: '{{ route('routedeivation.playdata') }}',
                type: 'GET',
                dataType: 'json',
                data: {
                    start_time: start_time, // Add your data here
                    end_time: end_time, // Add your data here
                    route_name: route_name, // Add your data here
                    route_id: route_id //
                },
                success: function(res) {
                    var playbackData = res.playback;
                    var planned_route1 = res.route_polyline;
                    var planned_route = planned_route1.polyline;
                    var locationData = res.location;
                    // console.log(planned_route1);
                    var latLngs = [];

                    if (fg) {
                        fg.clearLayers();
                        // fg.clearCircles();
                    }

                    $("#route_out_Location").text(locationData.route_out_address);
                    $("#route_in_Location").text(locationData.route_in_address);


                    for (var i = 0; i < planned_route1.length; i++) {
                        // latLngs.push(L.latLng(playbackData[i].latitude, playbackData[i].longitude));
                        var planned_polyline = L.Polyline.fromEncoded(planned_route1[i].polyline, {
                            weight: 3,
                            color: '#008000'
                        }).addTo(fg);
                    }

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

                    map.fitBounds(polyline.getBounds());

                }
            });


        }
    </script>
@endpush
