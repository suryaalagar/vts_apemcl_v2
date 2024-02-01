@extends('layouts.app')
@section('content')
    @push('scripts')
        <style>
            #map {
                height: 500px;
                width: 100%;
            }

            .input {
                position: absolute;
                top: 0;
                right: 0;
                margin-top: 8px;
            }
        </style>
    @endpush
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <section class="">
        <div class="row">
            <div class="col-md-12 addrouteassign">

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Add Trip plan</h4> <a class="heading-elements-toggle "><i
                                class="fa fa-ellipsis-v font-medium-3"></i></a>

                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form-horizontal form-simple" id="tripplanform" method="post" novalidate
                                enctype="multipart/form-data">
                                <div class="row">

                                    <div class="col-xl-2 col-lg-6 col-md-12">
                                        <fieldset class="form-group">
                                            <label for="start_location">Transaction Id:</label>
                                            <input type="text" class="form-control" name="poc_number" id="poc_number"
                                                placeholder="Enter Transaction Id">
                                        </fieldset>
                                    </div>

                                    <div class="col-md-3">
                                        <label>Trip Date</label>
                                        <div class="input-group">
                                            <input type='datetime-local' id="trip_date" name="trip_date"
                                                class="form-control startLocalDate" value=''>
                                        </div>
                                    </div>

                                    <div class="col-xl-3 col-lg-6 col-md-12">
                                        <fieldset class="form-group">
                                            <label for="vehicleid">Vehicle Name</label>
                                            <select class="form-control select2" id="vehicleid" name="vehicleid">
                                                <option>Select Vehicle</option>
                                                @if ($vehicle)
                                                    @foreach ($vehicle as $dlist)
                                                        <option value="{{ $dlist->device_imei }}">
                                                            {{ $dlist->vehicle_name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>

                                        </fieldset>
                                    </div>
                                    <div class="col-xl-3 col-lg-6 col-md-12">

                                    </div>
                                    <div class="col-xl-3 col-lg-6 col-md-12">
                                        <fieldset class="form-group">
                                            <label for="vehicleid">Generator Name</label>
                                            <select class="form-control select2" id="generator_id" name="generator_id">
                                                <option>Select Generator</option>
                                                @if ($generators)
                                                    @foreach ($generators as $glist)
                                                        <option value="{{ $glist->generator_id }}">
                                                            {{ $glist->generator_name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>

                                        </fieldset>
                                    </div>
                                    <div class="col-xl-3 col-lg-6 col-md-12">
                                        <fieldset class="form-group">
                                            <label for="vehicleid">Receiver Name</label>
                                            <select class="form-control select2" id="receiver_id" name="receiver_id">
                                                <option>Select Receiver</option>
                                                @if ($receivers)
                                                    @foreach ($receivers as $rlist)
                                                        <option value="{{ $rlist->receiver_id }}">
                                                            {{ $rlist->receiver_name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>

                                        </fieldset>
                                    </div>
                                    <div class="col-xl-3 col-lg-6 col-md-12">
                                        <fieldset class="form-group">
                                            <label>Route Name</label>
                                            <!--<input type="text" class="form-control" name="route_namedevi" id="route_namedevi" value="">-->
                                            <select name="route_id" class="select2 form-control" id="route_id">
                                                <option>Select Route</option>
                                            </select>

                                        </fieldset>
                                    </div>
                                    <div class="col-xl-3 col-lg-6 col-md-12">

                                    </div>





                                    <div class="col-xl-2 col-lg-6 col-md-12">
                                        <fieldset class="form-group">
                                            <button type="submit" id="submit_btn" style="margin-top: 25px;"
                                                class="btn btn-primary btn-min-width mr-1"></i> Create</button>
                                        </fieldset>
                                    </div>
                                    <div class="col-xl-2 col-lg-6 col-md-12">
                                        <fieldset class="form-group">
                                            <button type="button" style="margin-top: 25px;"
                                                class="btn btn-primary btn-min-width mr-1"
                                                onClick="window.location.reload();">Reset</button>
                                        </fieldset>
                                    </div>



                                    <input type="hidden" id="id" name="id" value="">

                                    <span id="alert_msg" style="color:red;"></span>
                                    <div id="map"></div>

                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection

@push('scripts')
    {{-- <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script> --}}
    <script>
        var osmUrl = 'http://{s}.tile.osm.org/{z}/{x}/{y}.png',
            osmAttrib = '&copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            osm = L.tileLayer(osmUrl, {
                maxZoom: 18,
                attribution: osmAttrib
            });

        // initialize the map on the "map" div with a given center and zoom
        var map = L.map('map').setView([14.642648038936546, 77.91915880384934], 5).addLayer(osm);

        var fg = L.featureGroup().addTo(map);


        $('#route_id').on('change', function() {
            var selectedRoute = $(this).val();

            $.ajax({
                type: 'GET',
                url: "{{ route('get_selected_polyline') }}",
                data: {
                    id: selectedRoute
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {

                    if (response.success == true) {

                        if (fg) {
                            fg.clearLayers();
                        }
                        var array = response.data;
                        if (array != '') {

                            var image_path = "{{ asset('assets/dist/img/starts.png') }}";
                            var image_path1 = "{{ asset('assets/dist/img/finish.png') }}";

                            var startm = [parseFloat(array.route_start_lat), parseFloat(array
                                .route_start_lng)];


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

                            var endm = [parseFloat(array.route_end_lat), parseFloat(array
                                .route_end_lng)];
                            var endmarker = L.marker(endm, {
                                icon: redIcon1
                            }).addTo(fg);
                            endmarker.bindPopup('End Location');

                            var encodedPolyline = array
                            .route_polyline; // Replace with your encoded polyline data
                            var planned_polyline = L.Polyline.fromEncoded(encodedPolyline, {
                                color: 'green'
                            }).addTo(fg);

                            map.fitBounds(planned_polyline.getBounds());


                        }
                    }
                    // var tripplan = response;



                },
            });

        });

        $('#receiver_id').on('change', function() {
            var receiver_id = $(this).val();
            var generator_id = $("#generator_id").val();
            var route_id_select = $("#route_id");
            loadroutes(generator_id, receiver_id, route_id_select)
        })

        function loadroutes(generator_id, receiver_id, route_id_select) {
            route_id_select.find('option').remove().end().append(
                "<option value=''> Select Route </option>");
            $.ajax({
                type: 'GET',
                url: "{{ route('generator_route_list') }}",
                data: {
                    generator_id: generator_id,
                    receiver_id: receiver_id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success == true) {
                        var array = response.data;
                        if (array != '') {
                            for (let i = 0; i < array.length; i++) {

                                route_id_select.append("<option value=" + array[i].id + ">" + "Route " + (i +
                                    1) + "</option>");
                            }
                        }
                    }
                },
            });
        }

        $("#tripplanform").submit(function(e) {
            var simid = $('#simid').val();
            e.preventDefault();
            var form = $(this);

            $.ajax({
                type: "POST",
                url: "{{ route('trip_plan.store') }}",
                data: form.serialize(), // serializes the form's elements.
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {

                    if (data.message == 'Success') {
                        toastr.success("Data Updated Successfully!", "INSERT", {
                            progressBar: !0
                        });
                        window.location.href = '{{ route('tripplanreport.index') }}';
                    }
                    if (data.message == 'validaton_error') {
                        toastr.warning('Validate Error!', "Decline", {
                            progressBar: !0
                        });
                    } else {
                        toastr.warning("Data Not Inserted!", "Decline", {
                            progressBar: !0
                        });
                    }
                }
            });


        });
    </script>
@endpush
