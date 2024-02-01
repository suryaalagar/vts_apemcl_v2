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
                                <input type='datetime-local' id="from_Date" name="fromdate" class="form-control startLocalDate">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label>To Date</label>
                            <div class="input-group">
                                <input type='datetime-local' name="todate" id="to_Date" class="form-control endLocalDate">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label>Select Vehicle</label>
                            <div class="input-group">

                                

                                <input type="hidden" id="path_coords">
                                <!-- <input type="text" id="settime_clear">  -->

                            </div>
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <div class="input-group-append" id="button-addon2">
                                <button id='submit' class="btn btn-primary" onclick="view_map()">Search</button>&nbsp;&nbsp; &nbsp;
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
                                                <div class="col-md-4" style="background: #f2efe9;text-align: center;color: black;">
                                                    <div class="form-group">
                                                        <button class="btn btn-success btn-play" id="start" type="button" value="Start" data-toggle="tooltip" title="Start"> <i class="fa fa-play"></i></button>
                                                        <button onclick="" class="btn btn-warning btn-pause hidden" data-toggle="tooltip" title="Stop"> <i class="fa fa-pause"></i></button>
                                                        <button onclick="close_map()" class="btn btn-danger btn-close" data-toggle="tooltip" title="Close"> <i class="fa fa-times"></i></button>
                                                        <select id="speed_limit" name="speed_limit" class="form-control" style="display: inline-block;width: 100px;" id="sel1">
                                                            <!--  <option value="-1">Park View</option>  -->
                                                            <option value="350">Medium</option>
                                                            <option value="700">Slow</option>
                                                            <option value="100">Fast</option>

                                                        </select>
                                                        <input type="checkbox" id="parkview">Park View
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
                                                        <button type="button" id='playback' style="background-color:#95418d; margin-top: 10px;" class="badge bg-blue pull-right" onclick="export_excel()"> <span class='fa fa-download'></span> &nbsp;&nbsp;Export Excel</button>
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

    </script>

    </body>
@endpush