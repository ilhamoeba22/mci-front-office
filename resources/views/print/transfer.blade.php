<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Transfer - {{ $token }}</title>
    <style>
        /* PRINT SPECIFIC CONFIG - EXACT LEGACY MATCH */
        @page {
            size: 210mm 150mm;
            margin: 0;
        }
        
        body {
            width: 210mm;
            height: 150mm;
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

        /* COORDINATE MAPPING (Translation of print_trf.php) */
        .tanggal      { top: 23mm; left: 39mm; }
        
        /* PENERIMA */
        .nama_tujuan  { top: 35mm; left: 55mm; }
        .almt_tujuan  { top: 40mm; left: 55mm; }
        .nominal_1    { top: 44mm; left: 170mm; width: 20mm; text-align: right; }
        .rek_tujuan   { top: 50mm; left: 55mm; font-size: 10pt; }
        .bank_tujuan  { top: 55mm; left: 55mm; }
        .biaya_trf    { top: 60mm; left: 170mm; width: 20mm; text-align: right; }
        .negara       { top: 70mm; left: 55mm; }
        .nominal_2    { top: 67mm; left: 170mm; width: 20mm; text-align: right; }
        .terbilang    { top: 73mm; left: 135mm; width: 60mm; white-space: normal; line-height: 4mm; }

        /* PENGIRIM */
        .nama_peng    { top: 83mm; left: 55mm; font-size: 7pt; }
        .almt_peng    { top: 87mm; left: 55mm; width: 50mm; white-space: normal; line-height: 4mm; font-size: 6pt; }
        .hp_peng      { top: 98mm; left: 55mm; font-size: 9pt; }
        .rek_peng     { top: 103mm; left: 72mm; font-size: 9pt; }
        .berita       { top: 88mm; left: 120mm; font-size: 7pt; }

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
    <div class="field tanggal">{{ \Carbon\Carbon::parse($data->created)->format('d / m / Y') }}</div>
    
    <!-- SECTION: PENERIMA -->
    <div class="field nama_tujuan">{{ $data->nama_tujuan }}</div>
    <div class="field almt_tujuan">{{ $data->alamat_tujuan }}</div>
    <div class="field nominal_1">{{ number_format($data->nominal, 0, ',', '.') }}</div>
    <div class="field rek_tujuan">{{ $data->no_rek_tujuan }}</div>
    <div class="field bank_tujuan">{{ $data->bank_tujuan }}</div>
    <div class="field biaya_trf">{{ number_format($data->biaya_trf, 0, ',', '.') }}</div>
    <div class="field negara">Indonesia</div>
    <div class="field nominal_2">{{ number_format($data->nominal, 0, ',', '.') }}</div>
    <div class="field terbilang">{{ $data->terbilang }}</div>

    <!-- SECTION: PENGIRIM -->
    <div class="field nama_peng">{{ $data->nama }}</div>
    <div class="field almt_peng">{{ $data->alamat_penyetor }}</div>
    <div class="field hp_peng">{{ $data->hp_penyetor }}</div>
    <div class="field rek_peng">{{ $data->no_rek }}</div>
    <div class="field berita">{{ $data->berita_tujuan }}</div>
</body>
</html>
