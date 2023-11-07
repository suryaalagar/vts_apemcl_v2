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
@push('styles')
    <style>
        #map {
            height: 100%;
        }
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
                                            <div>
                                                <label>Address
                                                    <input type="checkbox" id="myCheck1">
                                                </label>
                                            </div>
                                            <br />
                                            <h3 align="center">Idle Report</h3>

                                            <br />
                                            <table class="table table-striped table-bordered" id="datatable">
                                                <thead>
                                                    <tr>
                                                        <th>S.No</th>
                                                        <th>Vehicle Name</th>
                                                        <th>Start Location</th>
                                                        <th>End Location</th>
                                                        <th>Start Time</th>
                                                        <th>End Time</th>
                                                        <th>Duration</th>
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
                    <h4 class="modal-title" id="myModalLabel">Idle Report</h4>
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


    @push('scripts')
        <script>
            $(document).ready(function() {
                $("#selectdate1,#selectdate2,#myCheck1").change(function() {
                    get_report();
                });

                function get_report() {

                    var fromdate = $('#selectdate1').val();
                    var todate = $('#selectdate2').val();
                    var active_only = $('#myCheck1').is(':checked') ? 1 : 0;
                    $('#datatable').DataTable({

                        processing: true,
                        serverSide: true,
                        destroy: true,
                        // method: 'GET',
                        // ajax: "{{ route('parkingreport.getData') }}",
                        ajax: {
                            url: "{{ route('parkingreport.getData') }}",
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
                                data: 'vehicle_id',
                                name: 'vehicle_id'
                            },
                            {
                                data: 'device_imei',
                                name: 'device_imei'
                            },
                            {
                                data: 'start_datetime',
                                name: 'start_datetime'
                            },
                            {
                                data: 'end_datetime',
                                name: 'end_datetime'
                            },
                            {
                                data: 'park_address',
                                name: 'park_address'
                            },
                            {
                                data: 'duration',
                                name: 'duration'
                            },
                            {
                                data: 'Action',
                                name: 'Action'
                            }

                        ]

                    });
                }
            });
        </script>
    @endpush
@endsection

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

        function parking_data(lat, lng) {
            console.log(lat);
            console.log(lng);
            $("#myModal").modal("show");
            setTimeout(function() {
                map.invalidateSize();
                showMap(lat, lng);
            }, 200);
        }

        function showMap(lat, lng) {

            var mark_img = "{{ 'assets/dist/img/icon/marker_loc.png' }}";
            var redIcon = new L.Icon({
                iconUrl: mark_img
            });
            var startCoords = [lat, lng];
            console.log(startCoords);

            StartMarker1 = L.marker(startCoords, {
                icon: redIcon
            }).addTo(map);
            // map.setZoom(10);
            // console.log(map.getZoom());
            // map.setMinZoom(map.getZoom());            
            var group = new L.featureGroup([StartMarker1]);

            map.fitBounds(group.getBounds());
            // var popup = L.popup()
            //     .setContent("I am a standalone popup.");
            StartMarker1.bindPopup("hello").openPopup();
            // StartMarker1.bindPopup().openPopup();
            map.setView(startCoords, 12);
        }
    </script>

    </body>
@endpush
