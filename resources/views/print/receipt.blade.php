<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi #{{ $transaction->id }}</title>
    <style>
        @page {
            size: 58mm auto;
            margin: 0;
        }

        html, body {
            width: 58mm;
            margin: 0 auto;
            padding: 0;
            background: #fff;
        }

        body {
            font-family: Arial, Helvetica, sans-serif; /* Menggunakan sans-serif rapi sesuai acuan */
            font-size: 11px;
            line-height: 1.3;
            padding: 10px 6px;
            color: #000;
            box-sizing: border-box;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .logo {
            max-width: 45mm;
            height: auto;
            margin: 0 auto 5px auto;
            display: block;
        }

        .line { 
            border-bottom: 1px dashed #000; 
            margin: 6px 0; 
        }

        table { width: 100%; border-collapse: collapse; }
        td { padding: 1px 0; vertical-align: top; font-size: 11px; }

        .meta-table td {
            font-size: 10px;
        }

        @media print {
            html, body { width: 58mm !important; }
            body { padding: 4px; }
            /* Memastikan logo ter-render dengan baik saat diprint */
            img { -webkit-print-color-adjust: exact; }
        }
        @page { size: 80mm auto; margin: 0; }
        html, body { width: 80mm !important; background: #fff9ed !important; }
        body { box-sizing: border-box; padding: 7mm 6mm !important; color: #8c2e24 !important; font-family: Arial, Helvetica, sans-serif; font-size: 10px; }
        .logo { width: 31mm; max-width: 31mm; margin: 0 auto 1mm; }
        .receipt-title { margin: 1mm 0 4mm; border-top: 1.5px solid #d34a45; border-bottom: 1.5px solid #d34a45; padding: 2.5mm 0; color: #b93632; font-size: 16px; font-weight: 800; letter-spacing: .4px; }
        .text-center { text-align: center; } .text-right { text-align: right; } .font-bold { font-weight: 700; }
        table { width: 100%; border-collapse: collapse; } td { padding: 1.4mm 0; color: #6e2923; }
        .meta-table { border-radius: 3mm; background: #fde8e3; overflow: hidden; } .meta-table td { padding: 1.7mm 2mm; font-size: 9px; }
        .items-table { margin-top: 1mm; } .items-table th { padding: 2mm 1mm; background: #f9d9d3; color: #a62f2d; font-size: 9px; text-align: left; } .items-table td { border-bottom: 1px dashed #e8c6bd; padding: 2mm 1mm; vertical-align: top; }
        .total-table { margin-top: 2mm; border-radius: 3mm; background: #fde8e3; overflow: hidden; } .total-table td { padding: 1.6mm 2mm; } .total-table .grand-total td { background: #f5c8c1; color: #a62f2d; font-size: 12px; font-weight: 800; }
        .thanks { margin-top: 5mm !important; color: #b93632; font-size: 11px; font-weight: 700; }
        @media print { html, body { width: 80mm !important; } body { padding: 6mm !important; } * { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }
    </style>
</head>
<body onload="window.print()">

    <!-- LOGO FAV DIMSUM -->
    <!-- Ganti path asset logo sesuai lokasi file logo kamu (misal: asset('images/logo.png')) -->
    <img src="{{ asset("images/logofav.jpeg") }}" alt="Logo Fav Dimsum" class="logo">
    <div class="receipt-title text-center">STRUK PEMBELIAN</div>

    <div class="text-center">
        <div class="font-bold" style="font-size: 13px;">FAV DIMSUM</div>
        <div>{{ $transaction->branch->name ?? "Cabang Fav Dimsum" }}</div>
        <div style="font-size: 9px;">{{ $transaction->branch->address ?? "-" }}</div>
    </div>

    <div class="line"></div>

    <table class="meta-table">
        <tr><td><strong>No. Order</strong><br>#{{ $transaction->id }}</td><td class="text-right"><strong>Tanggal</strong><br>{{ $transaction->created_at->format("d/m/Y") }}</td></tr>
        <tr><td><strong>Kasir</strong><br>{{ auth()->user()->name ?? "Kasir" }}</td><td class="text-right"><strong>Waktu</strong><br>{{ $transaction->created_at->format("H:i") }} WIB</td></tr>
    </table>

    <div class="line"></div>

    <table class="items-table">
        <thead><tr><th style="width: 9%;">No.</th><th>Nama Menu</th><th style="width: 14%; text-align: center;">Qty</th><th class="text-right" style="width: 25%;">Total</th></tr></thead>
        <tbody>
        @php $totalQty = 0; @endphp
        @foreach($transaction->details as $index => $detail)
            @php $qty = $detail->quantity ?? $detail->qty ?? 0; $totalQty += $qty; @endphp
            <tr><td>{{ $index + 1 }}.</td><td><strong>{{ $detail->product->name ?? "Produk" }}</strong><br><small>Rp {{ number_format($detail->price, 0, ",", ".") }}</small></td><td style="text-align: center;">{{ $qty }}</td><td class="text-right">Rp {{ number_format($qty * $detail->price, 0, ",", ".") }}</td></tr>
        @endforeach
        </tbody>
    </table>

    <!-- TOTAL QTY & RINCIAN BAYAR -->
    <table class="total-table">
        <tr>
            <td>Total Jumlah : {{ $totalQty }}</td>
            <td></td>
        </tr>
        <tr><td colspan="2" style="height: 4px;"></td></tr>
        <tr>
            <td>Subtotal</td>
            <td class="text-right">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
        </tr>
        <tr class="grand-total">
            <td class="font-bold">Total</td>
            <td class="text-right font-bold">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Bayar ({{ strtoupper($transaction->payment_method) }})</td>
            <td class="text-right">Rp {{ number_format($transaction->paid_amount ?? $transaction->total_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Kembali</td>
            <td class="text-right">Rp {{ number_format(($transaction->paid_amount ?? $transaction->total_amount) - $transaction->total_amount, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="line"></div>

    <!-- FOOTER -->
    <div class="text-center thanks" style="margin-top: 8px;">
        <div>Terima kasih Telah Berbelanja</div>
        <div style="font-size: 9px; margin-top: 3px;">~ Fav Dimsum ~</div>
    </div>

</body>
</html>