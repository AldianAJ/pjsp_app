<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 5px;
        }

        .header-table {
            width: 100%;
            border: none !important;
            margin-bottom: 10px;
            font-size: 12px;
        }

        .header-table td {
            border: none;
        }

        .no-border {
            border: none !important;
            font-size: 12px;
        }

        .header-left {
            width: 30%;
            font-weight: bolder
        }

        .header-center {
            width: 30%;
        }

        .header-right {
            width: 30%;
            font-weight: bolder
        }

        .details {
            text-align: center;
            font-size: 12px;
            font-weight: bolder
        }

        .ttd-section {
            text-align: center;
            border: none !important;
            font-size: 12px;
        }

        .ttd-section td {
            padding: 20px 40px;
            border: none;
        }

        .total-row {
            font-weight: bold;
        }

        h3 {
            text-align: center
        }

        .no-padding {
            padding: 0 !important;
        }

        .no-top-border {
            border-top: none;
        }

        .no-bottom-border {
            border-bottom: none;
        }
    </style>
</head>

<body>

    <h3>SURAT JALAN</h3>

    <table class="header-table">
        <tr>
            <td class="header-left">
                <strong>PT. Rajawali Sumber Rejeki</strong><br>
                Jl. Raya Ngranggon - Kutorejo KM 03<br>
                Dusun Tamping, Desa Mojotamping<br>
                Kec. Bangsal, Kabupaten Mojokerto<br>
                Phone: (0353) 881783
            </td>
            <td class="header-center">

            </td>
            <td class="header-right">
                <strong>Mojokerto,
                    {{ $sj_header? \Carbon\Carbon::parse($sj_header->tgl)->locale('id')->translatedFormat('d F Y'): ''
                    }}
                </strong><br>
                Kepada : {{ $sj_header->nama }}<br>
                Alamat : {{ $sj_header->alamat_sj }}<br>
                {{ $sj_header->kota_sj }}
            </td>
        </tr>
    </table>
    <table class="no-border">
        <tr>
            <td class="no-border">
                <strong>No. Surat Jalan :</strong><br>
                {{ $sj_header->no_sj }}<br><br>
                <strong>Bersama ini kami menyerahkan barang sesuai dengan Purchase Order No :
                    {{ $sj_header->no_po }}</strong>
                <br>
                <strong>Dengan perincian sebagai berikut:</strong>
            </td>
        </tr>
    </table>
    <table class="details">
        <thead>
            <tr>
                <th>No</th>
                <th>Jenis Rokok</th>
                <th>Pita Cukai</th>
                <th>Qty Pack</th>
                <th>Keterangan</th>
                <th>No. Batch</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sj_details as $index => $detail)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $detail->spek }}</td>
                <td>{{ number_format($detail->pc, 0, ',', '.') }}</td>
                <td>{{ number_format($detail->qty_total, 0, ',', '.') }}</td>
                <td style="text-align: left;">{{ $detail->ket }}</td>
                <td>{{ $detail->no_batch ?? '-' }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3">Total</td>
                <td>{{ number_format($sj_details->sum('qty_total'), 0, ',', '.') }}</td>
                <td style="text-align: left;">{{ number_format($sj_details->sum('qty_karton'), 0, ',', '.') }} Karton
                </td>
                <td>-</td>
            </tr>
        </tbody>
    </table>
    <br>
    <table class="no-border">
        <tr>
            <td class="no-border">
                <strong>No. Pol Kendaraan : {{ $sj_header->no_pol }}</strong>
            </td>
            <td class="no-border" style="width: 15%">

            </td>
            <td class="no-border">
                <strong>No. Segel Kendaraan : {{ $sj_header->no_segel }}</strong>
            </td>
        </tr>
    </table>

    <table class="ttd-section">
        <tr>
            <td><strong>Penerima</strong></td>
            <td><strong>Pengantar</strong></td>
            <td><strong>Hormat Kami</strong></td>
        </tr>
        <tr>
            <td style="height: 100px">(<span style="display: inline-block; margin-left: 120px;"></span>)</td>
            <td style="height: 100px"><strong>( {{ $sj_header->driver }} )</strong></td>
            <td style="height: 100px"><strong>( Ibu. Lisna )</strong></td>
        </tr>
    </table>
</body>

</html>