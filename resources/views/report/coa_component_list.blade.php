<table>
  <thead>
    <tr>
      <th><strong>Nama Grup :</strong> </th>
      <th>{{$data['chart_of_account']['name']}}</th>
    </tr>
    <tr>
      <th>No</th>
      <th>ID</th>
      <th>Nama</th>
    </tr>
  </thead>
  <tbody>
    @php $i=1 @endphp
    @foreach($data['data'] as $d)
    <tr>
      <td>{{ $i++ }}</td>
      <td>{{$d['id']}}</td>
      <td>{{$d['name']}}</td>
    </tr>
    @endforeach
  </tbody>
</table>
