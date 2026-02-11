<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Setor Tunai - {{ $token }}</title>
    <style>
        /* PRINT SPECIFIC CONFIG - EXACT LEGACY MATCH */
        @page {
            size: 210mm 100mm;
            margin: 0;
        }
        
        body {
            width: 210mm;
            height: 100mm;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            font-size: 8pt;
            font-weight: bold;
            color: black;
            position: relative;
            background-color: white;
            overflow: hidden;
        }

        .field {
            position: absolute;
            white-space: nowrap;
        }

        /* COORDINATE MAPPING (Translation of print_data.php) */
        .tanggal      { top: 31mm; left: 32mm; }
        .nama         { top: 37mm; left: 37mm; }
        .no_rek       { top: 36mm; left: 142mm; }
        .nominal      { top: 42mm; left: 55mm; }
        .terbilang    { top: 46mm; left: 37mm; width: 70mm; white-space: normal; line-height: 4mm; }
        .tujuan       { top: 56mm; left: 47mm; }
        .nama_peny    { top: 61mm; left: 50mm; }
        .noid_peny    { top: 69mm; left: 50mm; }
        .almt_peny    { top: 72mm; left: 50mm; }
        .hp_peny      { top: 75mm; left: 50mm; }

        @media screen {
            body {
                border: 1px solid #ccc;
                margin: 20px auto;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
        }
    </style>
</head>
<body onload="window.print();">
    <div class="field tanggal">{{ \Carbon\Carbon::parse($data->created)->format('d/m/Y') }}</div>
    <div class="field nama">{{ $data->nama }}</div>
    <div class="field no_rek">{{ $data->no_rek }}</div>
    <div class="field nominal">{{ number_format($data->nominal, 2, ',', '.') }}</div>
    <div class="field terbilang">{{ $data->terbilang }}</div>
    <div class="field tujuan">{{ $data->tujuan }}</div>
    
    <!-- DATA PENYETOR -->
    <div class="field nama_peny">{{ $data->nama_penyetor }}</div>
    <div class="field noid_peny">{{ $data->noid_penyetor }}</div>
    <div class="field almt_peny">{{ $data->alamat_penyetor }}</div>
    <div class="field hp_peny">{{ $data->hp_penyetor }}</div>
</body>
</html>
