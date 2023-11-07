@extends('layouts.app')
@section('content')
    @push('styles')
        <style>
            #map {
                height: 100%;
            }
        </style>
    @endpush
    <link rel="stylesheet" href="http://vts.trackingwings.com/assets/dist/css/draw.css" />
    <link rel="stylesheet" href="http://vts.trackingwings.com/assets/dist/css/autocomplete.min.css" />
    <link rel="stylesheet" href="http://vts.trackingwings.com/assets/dist/css/routing-machine.css" />
    <section class="">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">ADD Route Devation </h4> <a class="heading-elements-toggle"><i
                                class="fa fa-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-action="close"><i class="feather icon-x"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-body imgcapturecard">
                            <form class="form-horizontal form-simple" id="polygonform" method="post" novalidate
                                enctype="multipart/form-data">

                                @csrf
                                <div class="row">
                                    <div class="row">



                                        <div class="col-xl-2 col-lg-6 col-md-12 mb-1">
                                            <fieldset class="form-group">
                                                <label for="polygon_name" class="required">Route Name<span
                                                        class="error">&nbsp;*</span></label>
                                                <input type="text" class="form-control" id="route_name" name="route_name"
                                                    placeholder="Enter The Area Name" required>
                                                <div class="div1" id="div1"></div>
                                            </fieldset>
                                        </div>
                                        <div class="col-xl-2 col-lg-6 col-md-12 mb-1">
                                            <fieldset class="form-group">
                                                <label for="polygon_name" class="required">Start Location<span
                                                        class="error">&nbsp;*</span></label>
                                                <input type="text" class="form-control" name="startlocation"
                                                    autocomplete="off" id="search" placeholder="Enter Start Location"
                                                    required>
                                                <div class="div1" id="div1"></div>
                                            </fieldset>
                                        </div>
                                        <div class="col-xl-2 col-lg-6 col-md-12 mb-1">
                                            <fieldset class="form-group">
                                                <label for="polygon_name" class="required">End Location<span
                                                        class="error">&nbsp;*</span></label>
                                                <input type="text" class="form-control" name="endlocation"
                                                    autocomplete="off" id="search1" placeholder="Enter End Location"
                                                    required>
                                                <div class="div1" id="div1"></div>
                                            </fieldset>
                                        </div>

                                        <!-- <input id="encoded" type="text"></input> -->
                                        <input type="text" name="encoded" id="encoded" value="">

                                        <div class="col-xl-4 col-lg-6 col-md-12 mb-1">
                                            <input type="button" class="btn btn-info btn-min-width mr-1 btn-next btn-next1"
                                                value="Submit" id='routesubmit' style="margin-top: 23px;">
                                            <button type="button" class="btn btn-primary btn-min-width" id="closeform"
                                                style="margin-top: 23px;">Reset</button>
                                        </div>

                                        <input type="hidden" id="polygon_arr" name="polygon_arr" value="">
                                        <div class="col-xl-12 col-lg- col-md-12 mb-1" id='TextBoxesGroup'>
                                            <div id="polygon_map" style="height:700px;width:100%;">
                                            </div>
                                        </div>

                                        <input type="hidden" name="polyline_lat_lng" id="polyline_lat_lng" value="">

                                        <input type="hidden" name="polygon_id" id="polygon_id" value="">
                                        <input type="hidden" name="counter_length" id="counter_length" value="">
                                        <input type="hidden" id="startlocation" name="startlatlang">
                                        <input type="hidden" id="endlocation" name="endlatlang">
                                        <input type="hidden" id="captimges" name="captimges">

                                        <div class="col-xl-12 col-lg-12 col-md-12">

                                        </div>



                                    </div>
                            </form>
                        </div>
                    </div>
                </div>
    </section>
@endsection

@push('scripts')
    <script src="http://vts.trackingwings.com/assets/plugins/osm/leaflet.js"></script>
    <script src="http://vts.trackingwings.com/assets/plugins/osm/autocomplete.min.js"></script>
    <script src="http://vts.trackingwings.com/assets/plugins/osm/leaflet-routing-machine.js"></script>
    <script src="http://vts.trackingwings.com/assets/plugins/osm/leaflet.draw.js"></script>
    <script src="http://vts.trackingwings.com/assets/plugins/osm/Polyline.encoded.js"></script>
    <link rel="stylesheet" href="http://vts.trackingwings.com/assets/dist/css/Control.Geocoder.css" />
    <script src="http://vts.trackingwings.com/assets/plugins/osm/Control.Geocoder.js"></script>

    <script>
        $(document).ready(function() {
            $("#routesubmit").click(function(e) {
                // alert("hello");
                var polygon_id = $('#polygon_id').val();
                e.preventDefault();
                var form = $("#polygonform");
                console.log(form);
                //return false;
                $.ajax({
                    type: "POST",
                    url: "{{ route('route.route_store') }}",
                    data: form.serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        console.log(data);
                        if (data.message == 'Success') {
                            toastr.success("Data Updated Successfully!", "INSERT", {
                                progressBar: !0
                            });
                            window.location.href = '{{ route('vehicle.index') }}';
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




        });



        map = L.map('polygon_map').setView([11.0467, 76.9254], 6);
        // L.tileLayer('http://198.204.245.190/osm/{z}/{x}/{y}.png',{maxZoom:18}).addTo(map);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 20
        }).addTo(map);

        var fg = L.featureGroup().addTo(map);
        var assetLayerGroup = new L.LayerGroup();


        // Initialise the FeatureGroup to store editable layers
        var editableLayers = new L.FeatureGroup();
        map.addLayer(editableLayers);


        new Autocomplete("search", {
            selectFirst: true,
            insertToInput: true,
            cache: true,
            howManyCharacters: 2,
            // onSearch
            onSearch: ({
                currentValue
            }) => {

                // api
                const api = `https://nominatim.openstreetmap.org/search?format=geojson&limit=20&city=${encodeURI(
    currentValue
    )}`;
                /**
                 * Promise
                 */
                return new Promise((resolve) => {
                    fetch(api)
                        .then((response) => response.json())
                        .then((data) => {
                            resolve(data.features);
                        })
                        .catch((error) => {
                            console.error(error);
                        });
                });
            },

            // nominatim GeoJSON format
            onResults: ({
                currentValue,
                matches,
                template
            }) => {
                const regex = new RegExp(currentValue, "gi");

                // if the result returns 0 we
                // show the no results element
                return matches === 0 ?
                    template :
                    matches
                    .map((element) => {
                        return `
    <li>
        <p>
            ${element.properties.display_name.replace(
            regex,
            (str) => `<b>${str}</b>`
            )}
        </p>
    </li> `;
                    })
                    .join("");
            },

            onSubmit: ({
                object
            }) => {
                // remove all layers from the map
                // map.eachLayer(function (layer) {
                // if (!!layer.toGeoJSON) {
                // map.removeLayer(layer);
                // }
                // });

                const {
                    display_name
                } = object.properties;
                const [lng, lat] = object.geometry.coordinates;
                // custom id for marker

                var startlocation = [lat, lng];
                $('#startlocation').val(startlocation);
                // const marker = L.marker([lat, lng], {
                // title: display_name,
                // });

                // marker.addTo(map).bindPopup(display_name);

                // map.setView([lat, lng], 8);
            },

            // get index and data from li element after
            // hovering over li with the mouse or using
            // arrow keys ↓ | ↑
            onSelectedItem: ({
                index,
                element,
                object
            }) => {
                // console.log("onSelectedItem:", { index, element, object });
            },

            // the method presents no results
            // no results
            noResults: ({
                    currentValue,
                    template
                }) =>
                template(`<li>No results found: "${currentValue}"</li>`),
        });

        new Autocomplete("search1", {
            selectFirst: true,
            insertToInput: true,
            cache: true,
            howManyCharacters: 2,
            // onSearch
            onSearch: ({
                currentValue
            }) => {

                // api
                const api = `https://nominatim.openstreetmap.org/search?format=geojson&limit=20&city=${encodeURI(
    currentValue
    )}`;

                return new Promise((resolve) => {
                    fetch(api)
                        .then((response) => response.json())
                        .then((data) => {
                            resolve(data.features);
                        })
                        .catch((error) => {
                            console.error(error);
                        });
                });
            },

            // nominatim GeoJSON format
            onResults: ({
                currentValue,
                matches,
                template
            }) => {
                const regex = new RegExp(currentValue, "gi");

                // if the result returns 0 we
                // show the no results element
                return matches === 0 ?
                    template :
                    matches
                    .map((element) => {
                        return `
    <li>
        <p>
            ${element.properties.display_name.replace(
            regex,
            (str) => `<b>${str}</b>`
            )}
        </p>
    </li> `;
                    })
                    .join("");
            },

            onSubmit: ({
                object
            }) => {
                const {
                    display_name
                } = object.properties;
                const [lng, lat] = object.geometry.coordinates;

                var endlocation = [lat, lng];
                $('#endlocation').val(endlocation);
                var startlocation = $('#startlocation').val();
                startlocations = startlocation.trim();
                startlocation = startlocations.split(",")[0];
                startlocation1 = startlocations.split(",")[1];
                var geocoder = L.Control.Geocoder.nominatim()

                var routing_latlng = L.Routing.control({
                    // geocoder: geocoder,
                    show: false,
                    waypoints: [
                        L.latLng([startlocation, startlocation1]),
                        L.latLng([lat, lng])
                    ],
                    routeWhileDragging: false,
                }).on('routeselected', function(e) {
                    var route = e.route;
                    var poly_line = L.polyline(e.route.coordinates);
                    var encode_latlngs = '';
                    var encode_latlngs = L.DomUtil.get('encoded').innerHTML += poly_line.encodePath();
                    $("#encoded").val(encode_latlngs);
                }).addTo(map);
                geocoder.geocode('Montreal', function(a, b) {});

            },
            onSelectedItem: ({
                index,
                element,
                object
            }) => {
                // ("onSelectedItem:", { index, element, object });
            },

            // the method presents no results
            // no results
            noResults: ({
                    currentValue,
                    template
                }) =>
                template(`<li>No results found: "${currentValue}"</li>`),
        });


        var polygon;
        var drawPluginOptions = {
            position: 'topright',
            draw: {
                polygon: {
                    allowIntersection: false, // Restricts shapes to simple polygons
                    drawError: {
                        color: '#e1e100', // Color the shape will turn when intersects
                        message: '<strong>Oh snap!<strong> you can\'t draw that!' // Message that will show when intersect
                    },
                    shapeOptions: {
                        color: '#97009c'
                    }
                },
                // disable toolbar item by setting it to false
                polyline: false,
                circle: false, // Turns off this drawing tool
                rectangle: false,
                marker: false,
            },
            edit: {
                featureGroup: editableLayers, //REQUIRED!!
                remove: false
            }
        };
        // Initialise the draw control and pass it the FeatureGroup of editable layers
        var drawControl = new L.Control.Draw(drawPluginOptions);
        map.addControl(drawControl);
        var editableLayers = new L.FeatureGroup();
        map.addLayer(editableLayers);

        map.on('draw:created', function(e) {
            var type = e.layerType,
                layer = e.layer;
            editableLayers.addLayer(layer);
            // console.log(layer.getLatLngs())
            polyvalue = JSON.stringify(layer.getLatLngs());
            $('#polygon_arr').val(polyvalue);
        });

        map.on('click', function(e) {
            // Check if there are already two waypoints set (start and end)
            if (drawControl.getWaypoints().length >= 2) {
                // Clear existing waypoints if there are more than two
                drawControl.setWaypoints([e.latlng]);
            } else {
                // Add a new waypoint (either start or end)
                drawControl.spliceWaypoints(drawControl.getWaypoints().length - 1, 1, e.latlng);
            }
        });


        map.on("draw:edited", function(e) {
            let layers = e.layers;
            layers.eachLayer(function(layer) {
                console.log(layer);
            });
        });
    </script>
@endpush
