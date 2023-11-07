@extends('layouts.app')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="content-wrapper" style="padding-bottom: 0px;padding-top: 3px;">
        <!--            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2">
                    <h3 class="content-header-title mb-0">Playback Report</h3>
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/">Home</a> </li>
                                <li class="breadcrumb-item active">Playback Report</li>
                            </ol>
                        </div>
                    </div>
                </div>
              
            </div>-->


        <section id="form-repeater">
            <div class="row">
                <div class="col-12">
                    <!--  <form action="#" method="POST"> -->
                    <div class="row">

                        <div class="col-md-3">
                            <label>From Date</label>
                            <div class="input-group">
                                <input type='device_datetime-local' id="from_Date" name="fromdate"
                                    class="form-control startLocalDate" value='{{ $from_date }}'>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label>To Date</label>
                            <div class="input-group">
                                <input type='device_datetime-local' name="todate" id="to_Date"
                                    class="form-control endLocalDate" value='{{ $to_date }}'>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label>Select Vehicle</label>
                            <div class="input-group">
                                <select class="select2 form-control" id="vehicle_imei" name="vehicle_imei">
                                    <option>Select Vehicle Name</option>
                                    @if ($vehicle)
                                        @foreach ($vehicle as $dlist)
                                            <option value="{{ $dlist->device_imei }}">
                                                {{ $dlist->vehicle_name }}</option>
                                        @endforeach
                                    @endif
                                </select>

                                <input type="hidden" id="path_coords">
                                {{-- <input type="text" id="settime_clear">  --}}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <div class="input-group-append" id="button-addon2">
                                <button id='submit' class="btn btn-primary"
                                    onclick="view_map()">Search</button>&nbsp;&nbsp;
                                &nbsp;
                                <button onclick="close_map()" class="btn btn-primary" type="submit">Clear</button>
                            </div>

                        </div>
                    </div>
                    <!--   </form> -->
                </div>
            </div>
        </section>
        <div class="clearfix"><br></div>
        <div class="content-body">
            <section id="configuration">
                <div class="row">
                    <div class="col-12">
                        <div class="card" style="margin-bottom: 0px;">
                            <!--                        <div class="card-header">
                            <h4 class="card-title">Configuration option</h4>
                            <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                            <div class="heading-elements">
                                <ul class="list-inline mb-0">
                                    <li><a data-action="collapse"><i class="feather icon-minus"></i></a></li>
                                    <li><a data-action="reload"><i class="feather icon-rotate-cw"></i></a></li>
                                    <li><a data-action="expand"><i class="feather icon-maximize"></i></a></li>
                                    <li><a data-action="close"><i class="feather icon-x"></i></a></li>
                                </ul>
                            </div>
                        </div>-->
                            <div class="card-content1 collapse show text-white bg-gradient-y-blue box-shadow-0">
                                <div class="card-body1 card-dashboard1">
                                    <!--<div class="table-responsive">-->
                                    <div id="playback_map" style="height:445px;width:100%;"></div>
                                    <div class="ctrl-box map-box hidden" style="width:100%;min-height:4vh;">

                                        <div id="tools col-lg-12 col-md-12 col-sm-12 ">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <table>
                                                            <tr>
                                                                <td>Start Date </td>
                                                                <td> : </td>
                                                                <th id="plform_date"></th>
                                                            </tr>

                                                            <tr>
                                                                <td>End Date</td>
                                                                <td> : </td>
                                                                <th id="plto_date"></th>
                                                            </tr>

                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="col-md-4"
                                                    style="background: #f2efe9;text-align: center;color: black;">
                                                    <div class="form-group">
                                                        <button class="btn btn-success btn-play" id="start"
                                                            type="button" value="Start" data-toggle="tooltip"
                                                            title="Start"> <i class="fa fa-play"></i></button>
                                                        <button onclick="" class="btn btn-warning btn-pause hidden"
                                                            data-toggle="tooltip" title="Stop"> <i
                                                                class="fa fa-pause"></i></button>
                                                        <button onclick="close_map()" class="btn btn-danger btn-close"
                                                            data-toggle="tooltip" title="Close"> <i
                                                                class="fa fa-times"></i></button>
                                                        <select id="speed_limit" name="speed_limit"
                                                            class="form-control"
                                                            style="display: inline-block;width: 100px;"
                                                            id="sel1">
                                                            <!--  <option value="-1">Park View</option>  -->
                                                            <option value="350">Medium</option>
                                                            <option value="700">Slow</option>
                                                            <option value="100">Fast</option>

                                                        </select>
                                                        {{-- <input type="checkbox" id="parkview">Park View --}}
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <table>
                                                            <tr>
                                                                <td>Start Odometer </td>
                                                                <td> : </td>
                                                                <th id="start_odo"></th>
                                                            </tr>

                                                            <tr>
                                                                <td>End Odometer</td>
                                                                <td> : </td>
                                                                <th id="end_odo"></th>
                                                            </tr>
                                                            <tr>
                                                                <td>Total Travel KMs:</td>
                                                                <th id="total_distance"></th>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group">
                                                        <button type="button" id='playback'
                                                            style="background-color:#95418d; margin-top: 10px;"
                                                            class="badge bg-blue pull-right" onclick="export_excel()">
                                                            <span class='fa fa-download'></span> &nbsp;&nbsp;Export
                                                            Excel</button>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                    </div>

                                    <!--</div>-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>




    </div>
</div>
@push('scripts')
    <script type="text/javascript">
        var StartMarker = {};
        var parkmarkerss = [];
        var speedmarkerss = [];
        var polylinemarkers = [];
        var parkmarker;
        var speedmarker;
        var assetLayerGroup = new L.LayerGroup();
        var route_play;

        var map = L.map('playback_map').setView([10.84125, 79.84266000000001], 6);
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

        function view_map() {

            var pathCoords = '';
            var vehicle = $('#vehicle_imei').val();
            var from_date, to_date;
            $(".map-box").addClass('hide');
            $(".loading").removeClass('hide');
            from_date = $('#from_Date').val();
            to_date = $('#to_Date').val();
            var fda = from_date.split(" ");


            var fda1 = fda[0].split("/");


            var fd = fda1[1] + '/' + fda1[0] + '/' + fda1[2] + ' ' + fda[1] + ' ' + fda[2];


            var eda = to_date.split(" ");

            var eda1 = eda[0].split("/");

            var ed = eda1[1] + '/' + eda1[0] + '/' + eda1[2] + ' ' + eda[1] + ' ' + eda[2];

            $.ajax({
                url: "{{ route('playback.get_history') }}",
                cache: false,
                type: 'GET',
                dataType: "json",
                data: {
                    
                    'vehicle': vehicle,
                    'from_date': from_date,
                    'to_date': to_date,
                },
                beforeSend: function() {

                    $("body").addClass("loadings");
                },
                success: function(data) {

                    console.log(data);
                    polylinemarkers.forEach(function(item) {
                        map.removeLayer(item)
                    });
                    map.removeLayer(StartMarker);


                    for (var i = 0; i < parkmarkerss.length; i++) {

                        map.removeLayer(parkmarkerss[i]);
                    }
                    for (var i = 0; i < speedmarkerss.length; i++) {

                        map.removeLayer(speedmarkerss[i]);
                    }

                    // // console.log(data);



                    $('.filter-box').addClass('hide');
                    $('.search-button').addClass('hide');
                    var counter = 0;


                    var speed = $('#speed_limit').val();
                    pathCoords = new Array();
                    var details = new Array();
                    var path_coords;
                    var obj = data;
                    console.log(obj);

                    if (obj != false) {
                        $('#settime_clear').val(1);
                        // clearInterval(route_play);
                        var vehicle_name = obj[0].vehicle_name;
                        var total = obj.length;

                        var start_odo = obj[0].odometer;
                        var end_odo = obj[total - 1].odometer;

                        $('#plform_date').empty().append(obj[0].device_datetime);
                        $('#plto_date').empty().append(obj[total - 1].device_datetime);

                        // console.log(start_odo);
                        // console.log(end_odo);
                        // console.log(end_totalodo);

                        var total_odo = (parseFloat(end_odo) - parseFloat(start_odo)).toFixed(3);

                        $('#start_odo').empty().append(start_odo);
                        $('#end_odo').empty().append(end_odo);
                        $('#total_distance').empty().append(total_odo + ' Kms');
                        $('#form_date').empty().append(from_date);
                        $('#to_date').empty().append(to_date);


                        $('.map-box').removeClass('hidden');
                        $(".loading").addClass('hide');

                        // var vehicle_type = obj[0].vehicletype;

                        var image_path = "{{ asset('assets/dist/img/ICONS/YELLOW/truck.png') }}";



                        var latlngs = [];

                        for (i = 0; i < obj.length; i++) {
                            //console.log(obj[i].latitude);
                            var device_datetime = "'" + obj[i].device_datetime + "'";
                            latlngs.push([obj[i].latitude, obj[i].longitude, device_datetime]);

                            pathCoords.push('{ lat :' + parseFloat(obj[i].latitude).toFixed(4) + ',lng : ' +
                                parseFloat(obj[i].longitude).toFixed(4) + ',ign :' + obj[i].ignition +
                                ',odometer :' + parseFloat(obj[i].odometer).toFixed(3) + ',speed : ' +
                                parseFloat(obj[i].speed) + ',angle : ' + parseFloat(obj[i].angle) +
                                ',modified_date : ' + device_datetime + '}');
                            vehiclename = obj[i].vehicle_name;

                        }
                        // console.log(pathCoords);

                        var polyline = L.polyline(latlngs, {
                            strokeColor: 'rgba(0, 128, 255, 0.7'
                        }).addTo(map);
                        var image_path = "{{ asset('assets/dist/img/starts.png') }}";
                        var image_path1 = "{{ asset('assets/dist/img/finish.png') }}";

                        var startm = [obj[0].latitude, obj[0].longitude];

                        var redIcon = new L.Icon({
                            iconUrl: image_path,
                            iconSize: [65, 65],
                            className: 'starticons',
                        });

                        var redIcon1 = new L.Icon({
                            iconUrl: image_path1,
                            iconSize: [65, 65],
                        });

                        polyline.on('click', (e) => {
                            console.log(e)
                            var point = findClosestPoint(e.latlng);
                            var text = point[2];
                            console.log(text)

                            var popup = L.popup()
                                .setLatLng(e.latlng)
                                .setContent(text)
                                .openOn(map);

                            //      polyline.bindPopup('sdfsd');
                            // this.openPopup();

                            //  polyline.bindTooltip(text).openPopup();

                            //  document.getElementById('testspan').innerHTML = text;
                        })

                        function findClosestPoint(latlng) {
                            var closestPoint = null;
                            var distance = 0;
                            latlngs.forEach((point) => {
                                if (closestPoint == null || distance > L.latLng([point[0], point[1]])
                                    .distanceTo(latlng)) {
                                    distance = L.latLng([point[0], point[1]]).distanceTo(latlng);
                                    closestPoint = point;
                                }
                            });
                            return closestPoint;
                        }

                        // marker = L.marker(startCoords,{icon: redIcon,rotationAngle: angle}).addTo(map);

                        var startmarker = L.marker(startm, {
                            icon: redIcon
                        }).addTo(map);
                        startmarker.bindPopup('Start Location');

                        var endm = [obj[obj.length - 1].latitude, obj[obj.length - 1].longitude];
                        var endmarker = L.marker(endm, {
                            icon: redIcon1
                        }).addTo(map);
                        endmarker.bindPopup('End Location');


                        var arrowHead = L.polylineDecorator(polyline, {
                            patterns: [{
                                offset: 25,
                                repeat: 50,
                                symbol: L.Symbol.arrowHead({
                                    pixelSize: 10,
                                    pathOptions: {
                                        color: '#23ba71',
                                        fillOpacity: 1,
                                        weight: 0
                                    }
                                })
                            }]
                        }).addTo(map);
                        console.log(arrowHead);
                        map.fitBounds(polyline.getBounds());
                        polylinemarkers.push(polyline);
                        polylinemarkers.push(arrowHead);
                        polylinemarkers.push(startmarker);
                        polylinemarkers.push(endmarker);

                        assetLayerGroup.addLayer(polylinemarkers);
                        path_coords = eval("[" + pathCoords + "]");
                        $('#path_coords').val(pathCoords);
                        //  console.log(polylinemarkers);
                        // alert(route_play);
                        clearInterval(route_play);
                    } else {
                        //   alert(obj);
                        console.log('obj');
                        alert('No data Found');

                        for (var i = 0; i < parkmarkerss.length; i++) {

                            map.removeLayer(parkmarkerss[i]);
                        }
                        for (var i = 0; i < speedmarkerss.length; i++) {

                            map.removeLayer(speedmarkerss[i]);
                        }

                        $('.map-box').addClass('hidden');
                        $(".loading").addClass('hidden');
                        $('.filter-box').removeClass('hidden');
                        $('.search-button').removeClass('hidden');
                    }

                    function stop_paly() {
                        clearTimeout(route_play);
                    }

                    //START HIDE AND SHOW PLAY PAUSE BUTTON
                    $('.btn-play').click(function() {


                        $('.btn-pause').removeClass('hidden');
                        $('.btn-play').addClass('hidden');
                        start_history();
                    });
                    $('.btn-pause').click(function() {
                        $('.btn-pause').addClass('hidden');
                        $('.btn-play').removeClass('hidden');
                        stop_paly();
                    });
                    //END HIDE AND SHOW PLAY PAUSE BUTTON

                    $('#parkview').click(function() {

                        if ($("#your-checkbox-id").is(":checked")) {
                            park_marker(vehicle_name);

                        } else {

                            for (var i = 0; i < parkmarkerss.length; i++) {

                                map.removeLayer(parkmarkerss[i]);
                            }
                            for (var i = 0; i < speedmarkerss.length; i++) {

                                map.removeLayer(speedmarkerss[i]);
                            }
                        }



                    });

                    function start_history() {

                        if ($('#speed_limit').val() == -1) {



                            park_marker(vehicle_name);

                        } else {

                            for (var i = 0; i < parkmarkerss.length; i++) {

                                map.removeLayer(parkmarkerss[i]);
                            }
                            for (var i = 0; i < speedmarkerss.length; i++) {

                                map.removeLayer(speedmarkerss[i]);
                            }
                            var path_coordval = $('#path_coords').val();
                            path_coords = eval("[" + path_coordval + "]");
                            //     console.log(path_coords);
                            var lat = path_coords[counter].lat;
                            var lng = path_coords[counter].lng;
                            //   console.log(lat);
                            //   console.log(lng);
                            var ign = path_coords[counter].ign;
                            var odometer = path_coords[counter].odometer;
                            var speed = path_coords[counter].speed;
                            var modified_date = path_coords[counter].modified_date;
                            var angle = path_coords[counter].angle;

                            moveMarker(lat, lng, counter, ign, odometer, speed, modified_date, angle);

                            counter++;

                            if (counter <= total - 1) {
                                route_play = setTimeout(function() {
                                    start_history()
                                }, $('#speed_limit').val());

                            } else {
                                clearInterval(route_play);
                            }

                        }

                    }

                    function moveMarker(lat, lng, counter, ign, odometer, speed, modified_date, angle) {



                        var MarkerContent = "<div class='marker'>" +
                            "Vehicle No: <b>" + vehiclename + "</b>" +
                            "<br>Speed : <b>" + Math.round(speed) + "</b> Km/Hr" +

                            "<br>Odometer : <b>" + odometer + "</b>" +
                            "<br>Date Time : <b>" + modified_date + "</b>" +
                            "</div>";

                        var redIcon = new L.Icon({
                            iconUrl: image_path,
                            iconSize: [35, 35],
                        });


                        map.removeLayer(StartMarker);


                        // StartMarker = L.marker([lat, lng],{icon: redIcon,rotationAngle: angle}).addTo(map);
                        StartMarker = L.marker([lat, lng]).addTo(map);
                        // StartMarker = L.marker([lat, lng]).addTo(map);
                        map.addLayer(StartMarker);

                        var group = new L.featureGroup([StartMarker]);


                        StartMarker.bindPopup(MarkerContent).openPopup();


                    }

                },
                error: function(xhr) { // if error occured
                    alert("No data Founds");

                }
            });

        }
    </script>

    </body>
@endpush
