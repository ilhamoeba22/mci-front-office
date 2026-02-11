<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Tarik Tunai - {{ $token }}</title>
    <style>
        /* PRINT SPECIFIC CONFIG - EXACT LEGACY MATCH */
        @page {
            size: 210mm 100mm;
            margin: 0;
        }
        
        body {
            width: 210mm;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            font-size: 8pt;
            font-weight: bold;
            color: black;
            background-color: white;
        }

        .page {
            width: 210mm;
            height: 100mm;
            position: relative;
            overflow: hidden;
            page-break-after: always;
        }

        .field {
            position: absolute;
            white-space: nowrap;
        }

        /* PAGE 1: DEPAN */
        .p1-tanggal   { top: 38mm; left: 32mm; }
        .p1-nama      { top: 44mm; left: 54mm; }
        .p1-no_rek    { top: 44mm; left: 147mm; }
        .p1-nominal   { top: 51mm; left: 52mm; }
        .p1-terbilang { top: 50mm; left: 135mm; width: 70mm; white-space: normal; line-height: 4mm; }
        .p1-tujuan    { top: 56mm; left: 34mm; }

        /* PAGE 2: BELAKANG */
        .p2-nama      { top: 21mm; left: 144mm; }
        .p2-noid      { top: 32mm; left: 144mm; }
        .p2-hp        { top: 43mm; left: 144mm; }
        .p2-alamat    { top: 56mm; left: 144mm; width: 70mm; white-space: normal; line-height: 4mm; }

        @media screen {
            .page {
                border: 1px solid #ccc;
                margin: 20px auto;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
        }
    </style>
</head>
<body onload="window.print();">
    <!-- PAGE 1 -->
    <div class="page" id="page-1">
        <div class="field p1-tanggal">{{ \Carbon\Carbon::parse($data->created)->format('d/m/Y') }}</div>
        <div class="field p1-nama">{{ $data->nama }}</div>
        <div class="field p1-no_rek">{{ $data->no_rek }}</div>
        <div class="field p1-nominal">{{ number_format($data->nominal, 2, ',', '.') }}</div>
        <div class="field p1-terbilang">{{ $data->terbilang }}</div>
        <div class="field p1-tujuan">{{ $data->tujuan }}</div>
    </div>

    <!-- PAGE 2 -->
    <div class="page" id="page-2">
        <div class="field p2-nama">{{ $data->nama_penarik }}</div>
        <div class="field p2-noid">{{ $data->noid_penarik }}</div>
        <div class="field p2-hp">{{ $data->hp_penarik }}</div>
        <div class="field p2-alamat">{{ $data->alamat_penarik }}</div>
    </div>
</body>
</html>
