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
                border-radius: 10px;
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
    <!--stats-->
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-12">
            <div class="card" style="border-radius: 10px;">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media">
                            <div class="media-body text-left w-100">
                                <h3 class="primary">{{ $planned_trips }}</h3>
                                <span>Planned Trips</span>
                            </div>
                            <div class="media-right media-middle">
                                <i class="fa fa-truck primary font-large-2 float-right"></i>
                            </div>
                        </div>
                        <div class="progress progress-sm mt-1 mb-0">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 80%" aria-valuenow="25"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-12">
            <div class="card" style="border-radius: 10px;">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media">
                            <div class="media-body text-left w-100">
                                <h3 class="warning">{{ $process_trips }}</h3>
                                <span>Trip In Processing</span>
                            </div>
                            <div class="media-right media-middle">
                                <i class="fa fa-truck warning font-large-2 float-right"></i>
                            </div>
                        </div>
                        <div class="progress progress-sm mt-1 mb-0">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 40%" aria-valuenow="25"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-12">
            <div class="card" style="border-radius: 10px;">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media">
                            <div class="media-body text-left w-100">
                                <h3 class="success">{{ $completed_trips }}</h3>
                                <span>Trip Completed</span>
                            </div>
                            <div class="media-right media-middle">
                                <i class="fa fa-truck success font-large-2 float-right"></i>
                            </div>
                        </div>
                        <div class="progress progress-sm mt-1 mb-0">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 60%" aria-valuenow="25"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-12">
            <div class="card" style="border-radius: 10px;">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media">
                            <div class="media-body text-left w-100">
                                <h3 class="danger">0</h3>
                                <span>Trip Over Due</span>
                            </div>
                            <div class="media-right media-middle">
                                <i class="fa fa-truck danger font-large-2 float-right"></i>
                            </div>
                        </div>
                        <div class="progress progress-sm mt-1 mb-0">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: 60%" aria-valuenow="25"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row match-height">
        <div class="col-12">
            <div class="card" style="border-radius: 10px;">
                <div class="card-content">
                    <div class="card-body">
                        <div class="map" id="map">
                            <div style="width: 100px; height: 500px;" id="map_canvas"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script> --}}
@endsection
@push('scripts')
    <script type="text/javascript">
        // var marker;
        var markers = [];
        var map = L.map('map').setView([17.386533, 81.570239], 8);
        // create a new tile layer
        // var tileUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        //     layer = new L.TileLayer(tileUrl, {
        //         attribution: 'Maps © <a href=\"www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors',
        //         maxZoom: 15,
        //         noWrap: true,
        //     });
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 20
        }).addTo(map);

        var legend = L.control({
            position: "bottomleft"
        });
        legend.onAdd = function(map) {
            var div = L.DomUtil.create("div", "legend");
            div.innerHTML += "<h4>Vehicle Status</h4>";
            div.innerHTML += '<i style="background:green"></i><span>Moving</span><br>';
            div.innerHTML += '<i style="background:blue"></i><span>Parking</span><br>';
            div.innerHTML += '<i style="background:yellow"></i><span>Idle</span><br>';
            div.innerHTML += '<i style="background:grey"></i><span>No Network</span><br>';
            return div;
        };

        legend.addTo(map);

        // L.control.zoom({
        //     position: 'topright'
        // }).addTo(map);
        // add the layer to the map
        // Google Layer
        // var Google_layer = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        //     maxZoom: 20,
        //     subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        // });
        // map.addLayer(Google_layer);

        var fg = L.featureGroup().addTo(map);
        var assetLayerGroup = new L.LayerGroup();
        var first_time = true;
        $(document).ready(function() {

            update_all_data1();

        });

        setInterval(() => {
            update_all_data1();
        }, 3000);

        function update_all_data1() {

            // if(marker !==undefined){
            //         map.removeLayer(marker);
            //       }
            //       if(markers !==undefined){
            //         map.removeLayer(markers);
            //       }


            $.ajax({
                url: '{{ route('dashboard.all_vehicles') }}',
                type: 'POST',
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {

                    if (fg) {
                        fg.clearLayers();
                    }

                    for (i = 0; i < data.length; i++) {
                        // console.log(data[i]);
                        // var vehicle_sleep = parseInt(data[i].vehicle_sleep);
                        // var v_acc_on = parseInt(data[i].ignition);
                        // var v_speed = parseInt(data[i].speed);
                        // var angle = parseInt(data[i].angle);
                        // // var vehicletype = parseInt(data[i].vehicle_type);
                        // var v_u_time = parseInt(data[i].update_time);
                        // var colour = "";
                        // var current_status = "";
                        if (data[i].update_time <= 10) {
                            if (data[i].ignition == 1) {
                                if (data[i].speed > 0) {

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
                        var speed = data[i].speed == null ? 0 : data[i].speed;
                        var redIcon = new L.Icon({
                            iconUrl: image_path,
                            iconSize: [40, 40],
                        });

                        var angle = data[i].angle;
                        // var angle = ;
                        // var angle = ;

                        // var tooltipOptions = {
                        //     permanent: false,
                        //     sticky: true,
                        //     direction: 'top',
                        //     offset: [0, -30]
                        // };
                        // marker.bindTooltip(
                        //     "<div style='background:lightgreen;box-shadow: 0 1px 3px rgba(0,0,0,0.4);border: 0px solid lightyellow; padding:1px 3px 1px 3px'><b> Vehicle : " +
                        //     markerData.vehiclename + "<br>Speed:" + markerData.speed +
                        //     " Kms/Hr <br> Last Update at:" + markerData
                        //     .updatedon + "</b>", tooltipOptions).openTooltip();
                        // movingMarkers.push(marker);
                        var myLatLng = L.latLng([data[i].lattitute, data[i].longitute]);
                        var marker = new L.marker(myLatLng, {
                                icon: redIcon,
                                rotationAngle: angle
                            })
                            .bindPopup(" Vehicle  : " + data[i].vehicle_name)
                            .addTo(fg);

                        // map.removeLayer(marker);
                        map.addLayer(marker);

                        markers.push(marker);
                        //marker.addTo(map);
                        // }
                        // //         map.eachLayer(function(layer) {
                        // // if (!!layer.toGeoJSON) {
                        // //   map.removeLayer(layer);
                        // // }
                        // // });
                        assetLayerGroup.addLayer(marker);
                        // if (first_time) {
                        //     map.fitBounds(fg.getBounds());
                        // }
                        // map.fitBounds(fg.getBounds());




                    }

                }
            });

        }
    </script>

    </body>
@endpush
