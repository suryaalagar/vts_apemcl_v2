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

    .buttons-excel {
        background-color: #4CAF50;
        color: rgb(255, 255, 255);
        border: none;
        padding: 10px 20px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        cursor: pointer;
    }

    /* Style for the button container */
    .dt-buttons {
        margin-bottom: 20px;
    }

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 40px;
        height: 20px;
    }

    /* Hide the default checkbox input */
    .toggle-switch input {
        display: none;
    }

    /* Slider for the toggle switch */
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: 0.4s;
        border-radius: 20px;
    }

    /* Change color of slider when checked */
    .slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 2px;
        bottom: 2px;
        background-color: white;
        transition: 0.4s;
        border-radius: 50%;
    }

    /* Checkmark icon */
    .toggle-switch input:checked+.slider:before {
        transform: translateX(20px);
    }

    .address_text {
        position: relative;
        bottom: 12px;
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
                                            <input type="datetime-local" class="form-control" id="selectdate1" name="fromdate" value='{{ $from_date }}'>
                                        </div>
                                        <div class="form-group mb-1 col-sm-12 col-md-3">
                                            <label for="pass">To Date</label>
                                            <br>
                                            <input type="datetime-local" class="form-control" id="selectdate2" name="todate" value='{{ $to_date }}'>
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
                                        <h3 align="center">Stoppage Report</h3>

                                        <br />
                                        <table class="table table-striped table-bordered" id="datatable">
                                            <thead class="bg-primary">
                                                <tr>
                                                    <th>S.No</th>
                                                    <th>Vehicle Name</th>
                                                    <th>Device Imei</th>
                                                    <th>Start Time</th>
                                                    <th>End Time</th>
                                                    <th>Address</th>
                                                    <th>Duration</th>
                                                    <th>Map View</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                {{-- @php
                                                        $s_no = 1;
                                                    @endphp
                                                    @foreach ($parking_data as $park)
                                                        <tr>
                                                            {{-- <td>{{ $trip->client_id }}</td>
                                                <td>{{ $trip->vehicleid }}</td> --}}
                                                {{-- <td>{{ $s_no++ }}</td>
                                                <td>{{ $park->vehiclename }}</td>
                                                <td>{{ $park->start_location }}</td>
                                                <td>{{ $park->end_location }}</td>
                                                <td>{{ $park->start_time }}</td>
                                                <td>{{ $park->end_time }}</td>
                                                <td>{{ $park->duration }}</td>
                                                <td><button type="button" class="btn btn-success showModal" data-toggle="modal" data-target="#myModal" data-lat='17.538310' data-lng='79.210775'>
                                                        Map View
                                                    </button></td>
                                                </tr> --}}
                                                {{-- @endforeach --}}
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
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Parking Report</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body modal_offset">
                <div class="row">
                    <div class="col-md-12 modal_body_content">
                        <p>Location: <span id="stoppage_location"></span></p>
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

            var fromdate = $('#selectdate1').val();
            var todate = $('#selectdate2').val();
            var active_only = $('#myCheck1').is(':checked') ? 1 : 0;
            $.fn.dataTable.ext.errMode = 'throw';
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
                        data: 'vehicle_name',
                        name: 'vehicle_name'
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
{{-- @php
echo"complete";die;
@endphp --}}
@push('scripts')
<script type="text/javascript">
    var map = L.map('map').setView([10.84125, 79.84266000000001], 6);
    // create a new tile layer
    var StartMarker1 = [];
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 20
    }).addTo(map);

    // var tileUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
    //     layer = new L.TileLayer(tileUrl, {
    //         attribution: 'Maps © <a href=\"www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors',
    //         maxZoom: 20,
    //         noWrap: true,
    //     });
    // add the layer to the map
    // Google Layer
    // var Google_layer = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
    //     maxZoom: 20,
    //     subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    // });
    // map.addLayer(Google_layer);

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

        $.ajax({
            url: '{{ route('parkingreport.get_address') }}', // Update this with your actual route URL
            type: 'GET',
            data: {
                latitude: lat,
                longitude: lng
            },
            success: function(response) {

                $("#stoppage_location").text(response.address);

                var mark_img = "{{ asset('assets/dist/img/icon/marker_loc.png') }}";
                var redIcon = new L.Icon({
                    iconUrl: mark_img
                });
                var startCoords = [lat, lng];
                console.log(startCoords);

                StartMarker1 = L.marker(startCoords, {
                    icon: redIcon
                }).addTo(map);

                var markerBounds = L.latLngBounds(startCoords);
                map.fitBounds(markerBounds);
                StartMarker1.bindPopup("Stop Point").openPopup();
                map.setView(startCoords, 12);


            }
        });

    }
</script>

</body>
@endpush