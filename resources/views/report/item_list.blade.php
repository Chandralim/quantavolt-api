<table>
  <thead>
    <tr>
      <th>No</th>
      <th>Kategori</th>
      <th>Kode Barang</th>
      <th>Kode Produksi</th>
      <th>Nama</th>
      <th>Unit</th>
      <th>Stock</th>
    </tr>
  </thead>
  <tbody>
    @php $i=1 @endphp
    @foreach($data as $d)
    <tr>
      <td>{{ $i++ }}</td>
      <td>{{$d['category']}}</td>
      <td>{{$d['production_code']}}</td>
      <td>{{$d['code']}}</td>
      <td>{{$d['name']}}</td>
      <td>{{$d['unit']}}</td>
      <td>{{$d['stock']}}</td>
    </tr>
    @endforeach
  </tbody>
</table>
