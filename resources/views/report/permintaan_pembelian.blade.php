<table>
  <thead>
    <tr>
      <th>Tanggal</th>
      <th>No PP</th>
      <th>Keterangan</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>{{ myDateFormat($data['created_at'],"Y-m-d") }}</td>
      <td>{{ $data['no'] }}</td>
      <td>{{ $data['note'] }}</td>
    </tr>
  </tbody>
</table>
