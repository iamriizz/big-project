<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi #{{ $transaction->id }}</title>
    <style>
        /* Mengunci ukuran kertas printer thermal 58mm */
        @page {
            size: 58mm auto;
            margin: 0;
        }

        /* Mengunci tampilan di layar browser maupun cetak */
        html, body {
            width: 58mm;
            margin: 0 auto;
            padding: 0;
            background: #fff;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            line-height: 1.2;
            padding: 8px 5px;
            color: #000;
            box-sizing: border-box;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .line { border-bottom: 1px dashed #000; margin: 6px 0; }

        table { width: 100%; border-collapse: collapse; }
        td { padding: 1px 0; vertical-align: top; font-size: 11px; }

        @media print {
            html, body {
                width: 58mm !important;
            }
            body {
                padding: 2px 4px;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="text-center">
        <h3 style="margin: 0; font-size: 13px;">FAV DIMSUM</h3>
        <p style="margin: 2px 0;">{{ $transaction->branch->name ?? 'Cabang Fav Dimsum' }}</p>
        <p style="margin: 2px 0; font-size: 9px;">{{ $transaction->branch->address ?? '-' }}</p>
    </div>

    <div class="line"></div>

    <p style="margin: 2px 0;">No. Nota : #{{ $transaction->id }}</p>
    <p style="margin: 2px 0;">Tanggal  : {{ $transaction->created_at->format('d/m/Y H:i') }}</p>
    <p style="margin: 2px 0;">Kasir    : {{ auth()->user()->name ?? 'Admin' }}</p>

    <div class="line"></div>

    <table>
        @foreach($transaction->details as $detail)
            <tr>
                <td colspan="2" class="font-bold">{{ $detail->product->name ?? 'Produk' }}</td>
            </tr>
            <tr>
                <td>{{ $detail->qty }} x Rp{{ number_format($detail->price, 0, ',', '.') }}</td>
                <td class="text-right">Rp{{ number_format($detail->qty * $detail->price, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>

    <div class="line"></div>

    <table>
        <tr>
            <td class="font-bold">Total</td>
            <td class="text-right font-bold">Rp{{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Metode Bayar</td>
            <td class="text-right">{{ strtoupper($transaction->payment_method) }}</td>
        </tr>
    </table>

    <div class="line"></div>

    <div class="text-center">
        <p style="margin: 5px 0;">Terima Kasih Atas Kunjungannya!</p>
        <p style="margin: 2px 0; font-size: 9px;">~ Fav Dimsum ~</p>
    </div>

</body>
</html>