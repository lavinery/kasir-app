<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cetak Kartu Member</title>
    <style>
        body {
            background: #f5f5f5;
            font-family: Arial, Helvetica, sans-serif;
        }
        .box {
            position: relative;
            width: 320px;
            height: 200px;
            margin: 10px auto;
            background: #fff;
            border: 1px solid #333;
            border-radius: 8px;
            overflow: hidden;
        }
        .card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            padding: 10px;
            width: 100%;
            height: 100%;
            box-sizing: border-box;
        }
        .logo {
    position: absolute;
    top: 12pt; /* lebih turun */
    right: 14pt; /* lebih ke kiri */
    font-size: 16pt;
    font-family: Arial, Helvetica, sans-serif;
    font-weight: bold;
    color: #fff;
}
.logo p {
    text-align: right;
    margin-right: 12pt; /* lebih kiri */
}
        .logo img {
    position: absolute;
    margin-top: -2pt; /* atur naik/turun logo */
    width: 40px;
    height: 40px;
    right: 5pt;
}
        .nama {
            margin-top: 120px;
            font-size: 12pt;
            font-weight: bold;
            color: #fff;
        }
        .telepon {
            margin-top: 5px;
            font-size: 10pt;
            color: #fff;
        }
        .barcode {
            position: absolute;
            bottom: 10px;
            left: 10px;
            background: #fff;
            padding: 4px;
            border: 1px solid #333;
        }
        table {
            width: 100%;
        }
        td {
            vertical-align: top;
            text-align: center;
        }
    </style>
</head>
<body>
    <section>
        <table>
            @foreach ($datamember as $chunk)
                <tr>
                    @foreach ($chunk as $item)
                        <td>
                            <div class="box">
                                <img src="{{ public_path($setting->path_kartu_member) }}" alt="card" class="card-img">
                                <div class="overlay">
                                    <div class="logo">
                                        <p>{{ $setting->nama_perusahaan }}</p>
                                        <img src="{{ public_path($setting->path_logo) }}" alt="logo">
                                    </div>
                                    <div class="nama">{{ $item->nama }}</div>
                                    <div class="telepon">{{ $item->telepon }}</div>
                                </div>
                                <div class="barcode">
                                    <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG($item->kode_member, 'QRCODE') }}" alt="qrcode" height="50">
                                </div>
                            </div>
                        </td>
                    @endforeach
                    @if (count($chunk) == 1)
                        <td></td>
                    @endif
                </tr>
            @endforeach
        </table>
    </section>
</body>
</html>
