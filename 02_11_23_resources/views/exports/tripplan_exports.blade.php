<table>
    <thead>
        <tr>
            <th>S.No</th>
            <th>Trip id</th>
            <th>POC Number</th>
            <th>Device IMEI</th>
            <th>TripDate</th>
            <th>Vehicle Name</th>
            <th>Route Name</th>
            <th>Start Odometer</th>
            <th>End Odometer</th>
            <th>Start Time</th>
            <th>End Time</th>
        </tr>
    </thead>
    <tbody>
        @php $sno = 1; @endphp
        @foreach ($result as $row)
            <tr>
                <td>{{ $sno++ }}</td>
                <td>{{ $row->trip_id }}</td>
                <td>{{ $row->poc_number }}</td>
                <td>{{ $row->device_no }}</td>
                <td>{{ $row->trip_date }}</td>
                <td>{{ $row->vehicle_name }}</td>
                <td>{{ $row->route_name }}</td>
                <td>{{ $row->start_odometer }}</td>
                <td>{{ $row->end_odometer }}</td>
                <td>{{ $row->created_at }}</td>
                <td>{{ $row->updated_at }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
