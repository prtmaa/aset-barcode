<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 4px;
        }

        .label {
            padding: 10px;
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: middle;
        }

        .qr {
            width: 38%;
            text-align: center;
        }

        .qr img {
            width: 150px;
        }

        .info {
            width: 62%;
            text-align: center;
        }

        .logo img {
            width: 160px;
            margin: 0 auto 8px auto;
            display: block;
        }

        .nama {
            font-size: 16px;
            margin-bottom: 6px;
        }

        .kode {
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 1px;
        }
    </style>
</head>

<body>

    <div class="label">
        <table>
            <tr>
                <!-- KIRI: QR -->
                <td class="qr">
                    <img src="{{ $qr }}">
                </td>

                <!-- KANAN: INFO -->
                <td class="info">
                    <div class="logo">
                        <img src="{{ public_path('images/logo-wmu.png') }}">
                    </div>

                    <div class="nama">
                        {{ $asset->nama_aset }}
                    </div>

                    <div class="kode">
                        {{ $asset->kode_aset }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
