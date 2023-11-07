@extends('layouts.app')
@section('content')
    @push('styles')
        <style>
            #map {
                height: 100%;
            }
        </style>
    @endpush
    <!--stats-->
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-12">
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media">
                            <div class="media-body text-left w-100">
                                <h3 class="primary">100</h3>
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
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media">
                            <div class="media-body text-left w-100">
                                <h3 class="warning">35</h3>
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
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media">
                            <div class="media-body text-left w-100">
                                <h3 class="success">50</h3>
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
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media">
                            <div class="media-body text-left w-100">
                                <h3 class="danger">15</h3>
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
                        {{-- <button class="btn btn-default js-btn-popover" data-toggle="popover" data-trigger="focus"
                            data-placement="top" data-custom-class="popover-primary" title="Popover primary example"
                            data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus.">Popover primary
                        </button> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row match-height">
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
    </div>

    </div>

    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script> --}}
@endsection
@push('scripts')
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
        map.addLayer(Google_layer);
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
                type: 'GET',
                dataType: "json",
                success: function(data) {

                    if (fg) {
                        fg.clearLayers();
                    }

                    for (i = 0; i < data.length; i++) {
                        var angle = parseInt(data[i].angle);
                        if (data[i].update_time <= 10) {
                            if (data[i].ignition == 1) {
                                if (data[i].speed > 0) {

                                    var image_path = "{{ asset('assets/dist/img/ICONS/BLUE/truck.png') }}";

                                } else {
                                    var image_path = "{{ asset('assets/dist/img/ICONS/BLUE/truck.png') }}";
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

                        // var angle = data[i].angle;
                       
                        // var angles = 180;
                        // const angle = angles * (Math.PI / 180);
                        //     var tooltipOptions = {
                        //         permanent: false,
                        //         sticky: true,
                        //         direction: 'top',
                        //         offset: [0, -30]
                        //     };


                        marker = new L.marker([data[i].lattitute, data[i].longitute], {
                                icon: redIcon,
                                rotationAngle: data[i].angle
                            })
                            .bindPopup(" Vehicle  : " + data[i].vehicle_name)
                            .addTo(fg);
                        //         .bindTooltip("<div style='background:" + colour +
                        //             ";box-shadow: 0 1px 3px rgba(0,0,0,0.4);border: 0px solid lightyellow; padding:1px 3px 1px 3px'><b> Vehicle : " +
                        //             data[i].vehiclename + "</b><br><b>Status:" + current_status +
                        //             "</b><br><b>Speed:" + speed + "Km/Hr</b><br><b>Last Update:" + data[i]
                        //             .updatedon + "</b><br><b>Since:" + data[i].last_dur + "</b>", tooltipOptions)
                        //         .openTooltip()
                        //         //.bindPopup(" Vehicle  : " + data[i].vehiclename)
                        //         .addTo(fg);


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
