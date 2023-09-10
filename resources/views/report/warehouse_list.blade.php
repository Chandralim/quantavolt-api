<table>
  <thead>
    <tr>
      <th>No</th>
      <th>Kode</th>
      <th>Nama</th>
    </tr>
  </thead>
  <tbody>
    @php $i=1 @endphp
    @foreach($data as $d)
    <tr>
      <td>{{ $i++ }}</td>
      <td>{{$d['code']}}</td>
      <td>{{$d['name']}}</td>
    </tr>
    @endforeach
  </tbody>
</table>
