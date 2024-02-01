@extends('layouts.app')
@section('content')
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
                                            <h3 align="center">Parking Report</h3>

                                            <br />
                                            <table class="table table-striped table-bordered " id="datatable">
                                                <thead>
                                                    <tr>
                                                        <th>S.No</th>
                                                        <th>vehicle_name</th>
                                                        <th>Route Name</th>
                                                        <th>Route Deviate Out Time</th>
                                                        <th>Route Deviate Out Location</th>
                                                        <th>Route Deviate In Time</th>
                                                        <th>Route Deviate In Location</th>
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
                            <p><b>Route Out Location : Trichy Main Road</b></p>
                            <p><b>Route In Location  : Thogaimalai Junction Road</b></p>
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
            $('#datatable').DataTable({

                processing: true,
                serverSide: true,
                method: 'GET',
                ajax: "{{ route('parkingreport.getData') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'device_no',
                        name: 'device_no'
                    },
                    {
                        data: 'start_location',
                        name: 'start_location'
                    },
                    {
                        data: 'end_location',
                        name: 'end_location'
                    },
                    {
                        data: 'start_day',
                        name: 'start_day'
                    },
                    {
                        data: 'end_day',
                        name: 'end_day'
                    },
                    {
                        data: 'total_km',
                        name: 'total_km'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },

                ]

            });
        });
    </script>
@endpush`

@push('scripts')
    {{-- <link rel="stylesheet" href="{{ 'https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css' }}" />
    <script src="{{ 'https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js' }}"></script> --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js"></script> --}}
    {{-- <script src="script.js"></script> --}}
    <script type="text/javascript">
        var map = L.map('map').setView([10.84125, 79.84266000000001], 6);
        // create a new tile layer
        var tileUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            layer = new L.TileLayer(tileUrl, {
                attribution: 'Maps © <a href=\"www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors',
                maxZoom: 20,
                noWrap: true,
            });
        // add the layer to the map
        // Google Layer
        var Google_layer = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        });
        map.addLayer(Google_layer);
        $('.showModal').on('click', function() {
            // console.log(lat);
            var planned_route = $(this).data('planned_route');
            var actual_running = $(this).data('actual_running');
            setTimeout(function() {
                map.invalidateSize();

                showMap(planned_route, actual_running);
            }, 200);
        });

        function showMap(planned_route, actual_running) {


            var planned_polyline = L.Polyline.fromEncoded(planned_route, {
                weight: 3,
                color: '#008000'
            }).addTo(map);
            var running_polyline = L.Polyline.fromEncoded(actual_running, {
                weight: 3,
                color: '#FF0000'
            }).addTo(map);

            // var polygon = L.Polygon.fromEncoded(planned_route, {
            //     weight: 1,
            //     color: '#f30'
            // }).addTo(map);

            // var polygon = L.Polygon.fromEncoded(running_polyline, {
            //     weight: 1,
            //     color: '#FF0000'
            // }).addTo(map);

            map.fitBounds(planned_polyline.getBounds());
            // StartMarker1.bindPopup().openPopup();
            // map.setView(startCoords, 12);
        }
    </script>

    </body>
@endpush
