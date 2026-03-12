<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'helvetica', sans-serif; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 4px; }
        .header { font-weight: bold; text-align: center; font-size: 12pt; margin-bottom: 10px; }
        .sub-header { text-align: center; font-size: 10pt; margin-bottom: 10px; }
        .img-cell { height: 80px; text-align: center; }
        img { max-height: 70px; width: auto; }
    </style>
</head>
<body>
    <div class="header">DATA LABELISASI PVDC - PT. CHAROEN POKPHAND INDONESIA</div>
    
    @foreach($produks as $row)
        @php $items = json_decode($row->labelisasi, true) ?? []; @endphp
        
        <div class="sub-header" style="text-align: left; margin-top: 15px; background-color: #eee;">
            <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($row->date)->format('d-m-Y') }} | 
            <strong>Shift:</strong> {{ $row->shift }} | 
            <strong>Varian:</strong> {{ $row->nama_produk }} | 
            <strong>Operator:</strong> {{ $row->nama_operator }}
        </div>

        <table>
            <thead>
                <tr style="background-color: #ccc;">
                    <th width="5%">No</th>
                    <th width="20%">Mesin</th>
                    <th width="20%">Kode Batch</th>
                    <th width="30%">Bukti Gambar</th>
                    <th width="25%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $idx => $item)
                <tr nobr="true">
                    <td align="center">{{ $idx + 1 }}</td>
                    <td>{{ $item['mesin'] ?? '-' }}</td>
                    <td>{{ $item['kode_batch'] ?? '-' }}</td>
                    <td align="center" class="img-cell">
                        @if(!empty($item['file']))
                            <img src="{{ public_path(str_replace('/storage/', 'storage/', $item['file'])) }}">
                        @else - @endif
                    </td>
                    <td>{{ $item['keterangan'] ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</body>
</html>