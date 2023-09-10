<table>
  <thead>
    <tr>
      <th><strong>Kategori :</strong> </th>
      <th>{{$data['item']['category']}}</th>
      <th><strong>Kode :</strong> </th>
      <th>{{$data['item']['code']}}</th>
      <th><strong>Nama :</strong> </th>
      <th>{{$data['item']['name']}}</th>
      <th><strong>Satuan :</strong> </th>
      <th>{{$data['item']['unit']}}</th>
    </tr>
    <tr>
      <th>No</th>
      <th>Kode Gudang</th>
      <th>Keterangan</th>
      <th>Tipe</th>
      <th>Qty</th>
    </tr>
  </thead>
  <tbody>
    @php $i=1 @endphp
    @foreach($data['data'] as $d)
    <tr>
      <td>{{ $i++ }}</td>
      <td>{{$d['warehouse_code']}}</td>
      <td>{{$d['description']}}</td>
      <td>{{$d['is_out']}}</td>
      <td>{{$d['qty']}}</td>
    </tr>
    @endforeach
  </tbody>
</table>
