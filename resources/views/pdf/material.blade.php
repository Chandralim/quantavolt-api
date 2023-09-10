<html>

<head>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <!-- <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}"> -->
  <style>
    @page {
      margin: 130px 25px 100px 25px;
    }
  </style>
</head>

<body>
  <!-- Define header and footer blocks before your content -->
  <header class="fixed-top" style="top: -120px;">
    <table class="table table-bordered text-center">
      <tr>
        <td><img src="artifindo.png" width="200px" height="100px" /></td>
        <td class="align-middle">
          <h3>PT ARMADA KREATIF INDOPASIFIK</h3>
        </td>
        <td class="align-middle">
          KEBUTUHAN PROYEK MATERIAL
        </td>
      </tr>
    </table>
  </header>

  <!-- Wrap the content of your PDF inside a main tag -->
  <main>
    <div>
      <table class="table table-borderless ">
        <tbody>
          <tr>
            <td class="font-weight-bold">Nomor Project</td>
            <td> : {{$no_project}}</td>
            <td class="font-weight-bold">Tanggal</td>
            <td> : {{date('d-m-Y', strtotime($date))}}</td>
          </tr>
          <tr></tr>
          <tr></tr>
          <tr>
            <td class="font-weight-bold">Kebutuhan/Proyek</td>
            <td> : {{$proyek}}</td>
            <td class="font-weight-bold">Lokasi</td>
            <td> : {{$location}}</td>
          </tr>
        </tbody>
      </table>
      <table class="table table-sm table-bordered text-center">
        <thead class="text-center align-center">
          <tr>
            <th>No</th>
            <th>Kode Item</th>
            <th>Nama Item</th>
            <th>Satuan</th>
            <th>Jlh Asumsi</th>
            <th>Stok</th>
            <th>Asumsi Harga</th>
            <th>Jlh Realisasi</th>
            <th>Realisasi Harga</th>
            <th>Sub Total</th>
            <th>Keterangan</th>
          </tr>
        </thead>
        <tbody>
          @foreach($datas as $material=>$key)
          <tr>
            <td>{{$loop->iteration}}</td>
            <td>{{$key->item_code}}</td>
            <td>{{$key->item_name}}</td>
            <td>{{$key->unit_code}}</td>
            <td>{{$key->qty_assumption}}</td>
            <td>{{$key->stock}}</td>
            <td>{{$key->price_assumption}}</td>
            <td>{{$key->qty_realization}}</td>
            <td>{{$key->price_realization}}</td>
            <td>{{$key->price_realization * $key->qty_realization}}</td>
            <td>{{$key->note}}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <table class="table table-borderless text-center">
        <tr>
          <td>Diajukan</td>
          <td colspan="2">Disetujui</td>
          <td>Diproses</td>
        </tr>
        <tr>
          <td></td>
          <td>Teknik</td>
          <td>Finance</td>
          <td></td>
        </tr>
      </table>
    </div>
  </main>
</body>

</html>