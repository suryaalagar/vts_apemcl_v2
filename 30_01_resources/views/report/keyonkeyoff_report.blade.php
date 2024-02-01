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
                                            <h3 align="center">Key On Key Off Report</h3>

                                            <br />
                                            <table class="table table-striped table-bordered" id="datatable">
                                                <thead>
                                                    <tr>
                                                        <th>S.No</th>
                                                        <th>Vehicle Name</th>
                                                        <th>Start Time</th>
                                                        <th>End Time</th>
                                                        <th>Duration</th>
                                                        <th>Start Odo</th>
                                                        <th>End Odo</th>
                                                        <th>Distance (KM)</th>
                                                        <th>Map View</th>
                                                        <th>Start Location</th>
                                                        <th>End Location</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    {{-- @php
                                                        $s_no = 1;
                                                    @endphp
                                                    @foreach ($keyonkeyoff_data as $keyonkeyoff)
                                                        <tr>
                                                            <td>{{ $s_no++ }}</td>
                                                            <td>{{ $keyonkeyoff->vehiclename }}</td>
                                                            <td>{{ $keyonkeyoff->start_day }}</td>
                                                            <td>{{ $keyonkeyoff->end_day }}</td>
                                                            <td>{{ $keyonkeyoff->start_day }}</td>
                                                            <td>{{ $keyonkeyoff->start_odometer }}</td>
                                                            <td>{{ $keyonkeyoff->end_odometer }}</td>
                                                            <td>{{ $keyonkeyoff->end_odometer - $keyonkeyoff->start_odometer }}
                                                            </td>
                                                            <td><button type="button" class="btn btn-success showModal"
                                                                    data-toggle="modal" data-target="#myModal"
                                                                    data-lat='17.538310' data-lng='79.210775'>
                                                                    Map View
                                                                </button></td>
                                                            <td>{{ $keyonkeyoff->start_location }}</td>
                                                            <td>{{ $keyonkeyoff->end_location }}</td>

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
    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Key On Key Off Report</h4>
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
                    {
                        data: 'created_at',
                        name: 'created_at'
                    }, 
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },

                ]

            });
        });
    </script>
@endpush
