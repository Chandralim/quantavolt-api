<table>
  <thead>
    <tr>
    <th>No.</th>
    <th>Kode</th>
    <th>Nama</th>
    <th>Nama Contact Person</th>
    <th>Jabatan Contact Person</th>
    <th>Jenis Usaha</th>
    <th>No Telp</th>
    <th>NPWP</th>
    <th>Alamat</th>
    <th>Keterangan</th>
    </tr>
  </thead>
  <tbody>
    @php $i=1 @endphp
    @foreach($data as $d)
    <tr>
      <td>{{ $i++ }}</td>
      <td>{{$d['code']}}</td>
      <td>{{$d['name']}}</td>
      <td>{{$d['contact_person_name']}}</td>
      <td>{{$d['contact_person_position']}}</td>
      <td>{{$d['business_type']}}</td>
      <td>{{$d['phone_number']}}</td>
      <td>{{$d['tax_id_number']}}</td>
      <td>{{$d['address']}}</td>
      <td>{{$d['description']}}</td>
    </tr>
    @endforeach
  </tbody>
</table>
