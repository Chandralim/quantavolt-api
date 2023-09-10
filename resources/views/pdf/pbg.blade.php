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
          PENGELUARAN BARANG GUDANG (PBG)
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
            <td class="font-weight-bold">Nomor PBG</td>
            <td> : {{$pbg_no}}</td>
            <td></td>
            <td class="font-weight-bold">Tanggal PBG</td>
            <td> : {{date('d-m-Y', strtotime($date))}}</td>
          </tr>
          </tr>
          <tr></tr>
          <tr></tr>
          <tr>
            <td class="font-weight-bold">Nomor PAG</td>
            <td> : {{$pag_no}}</td>
            <td></td>
            <td class="font-weight-bold">Tanggal PAG</td>
            <td> : {{date('d-m-Y', strtotime($date_pag))}}</td>
          </tr>
          <tr></tr>
          <tr></tr>
          <tr>
            <td class="font-weight-bold">Kebutuhan/Proyek</td>
            @if($need)
            <td> : {{$need}}</td>
            @else
            <td> : {{$proyek ?? ""}}</td>
            @endif
            <td></td>
            <td class="font-weight-bold">Bagian</td>
            <td> : {{$part}}</td>
        </tbody>
      </table>
      <table class="table table-sm table-bordered text-center">
        <thead class="text-center">
          <tr>
            <th>No</th>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Jumlah</th>
            <th>Satuan</th>
            <th>Keterangan</th>
          </tr>
        </thead>
        <tbody>
          @foreach($datas as $pbg=>$key)
          <tr>
            <td>{{$loop->iteration}}</td>
            <td>{{$key->item_code}}</td>
            <td>{{$key->item->name}}</td>
            <td>{{$key->qty}}</td>
            <td>{{$key->item->unit->code}}</td>
            <td>{{$key->note}}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <table class="table table-borderless text-center">
        <tr>
          <td>Dibuat Oleh</td>
          <td>Disetujui Oleh</td>
          <td>Diterima Oleh</td>
        </tr>
      </table>
    </div>
  </main>
</body>

</html>