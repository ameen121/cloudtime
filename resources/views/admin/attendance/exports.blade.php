<table>

    @php
dd('dd');
    @endphp
    <thead>
        <tr>
        <th>Staff_id</th>
        <th>Staff_id</th>
        <th>Staff_id</th>
        <th>Staff_id</th>
        <th>Staff_id</th>
        <th>Staff_id</th>
        <th>Staff_id</th>
        <th>Staff_id</th>
        <th>Staff_id</th>
        <th>Staff_id</th>
    </tr>
    </thead>
    <tbody>
      @foreach($data as $datas)
    <tr>
        <td>
            {{ $datas['id']}}

        </td>
    </tr>
        @endforeach
        
    </tbody>
</table>