<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nota Kecil</title>

    <?php
    $style = '
    <style>
        * {
            font-family: "consolas", sans-serif;
        }
        p {
            display: block;
            margin: 3px;
            font-size: 10pt;
        }
        table td {
            font-size: 12pt;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-left {
            text-align: left;
        }
        .bold {
            font-weight: bold;
        }
        .total-section {
            border-top: 1px dashed #000;
            padding-top: 3px;
        }
        .grand-total {
            font-weight: bold;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 2px 0;
        }
        .discount-item {
            font-size: 9pt;
            color: #555;
            font-style: italic;
        }
        .savings-highlight {
            background-color: #f0f0f0;
            padding: 2px 0;
            font-weight: bold;
        }
        .price-crossed {
            text-decoration: line-through;
            color: #666;
            font-size: 10pt;
        }
        .price-discounted {
            color: #000;
            font-weight: bold;
            font-size: 11pt;
        }
        .discount-label {
            font-size: 8pt;
            color: #d00;
            font-weight: bold;
        }

        @media print {
            @page {
                margin: 0mm;
                size: 65mm 
    ';
    ?>
    <?php 
    $style .= 
        ! empty($_COOKIE['innerHeight'])
            ? $_COOKIE['innerHeight'] .'mm; }'
            : '}';
    ?>
    <?php
    $style .= '
            html, body {
        width: 65mm;
        margin: 0; 
        padding: 0mm; 
    }
            .btn-print {
                display: none;
            }
            .savings-highlight {
                background-color: transparent;
            }
        }
    </style>
    ';
    ?>

    {!! $style !!}
</head>
<body onload="window.print()">
    <button class="btn-print" style="position: absolute; right: 1rem; top: rem;" onclick="window.print()">Print</button>
    
    <!-- Header -->
    <div class="text-center">
        <h3 style="margin-bottom: 5px;">{{ strtoupper($setting->nama_perusahaan) }}</h3>
        <p>{{ strtoupper($setting->alamat) }}</p>
        @if(isset($setting->telepon) && $setting->telepon)
            <p>Telp: {{ $setting->telepon }}</p>
        @endif
    </div>
    <br>
    
    <!-- Transaction Info -->
    <div>
        <p style="float: left;">{{ date('d-m-Y H:i') }}</p>
        <p style="float: right">{{ strtoupper(auth()->user()->name) }}</p>
    </div>
    <div class="clear-both" style="clear: both;"></div>
    <p>No: {{ tambah_nol_didepan($penjualan->id_penjualan, 10) }}</p>
    
    @if(isset($penjualan->member) && $penjualan->member)
        <p>Member: {{ $penjualan->member->nama ?? 'Guest' }}</p>
    @endif
    
    <p class="text-center">=================================</p>
    
    <!-- Items dengan logika diskon yang BENAR -->
    <table width="100%" style="border: 0;">
        @php
            $total_item_discount = 0;
        @endphp
        
        @foreach ($detail as $item)
            @php
                // GUNAKAN data yang benar dari database
                // $item->diskon = diskon PRODUK (dari tabel produk atau penjualan_detail)
                // $penjualan->diskon = diskon MEMBER (dari settings)
                
                $diskon_produk = $item->diskon ?? 0; // Diskon produk individual
                
                // Jika tidak ada diskon di detail, ambil dari produk
                if ($diskon_produk == 0 && isset($item->produk->diskon)) {
                    $diskon_produk = $item->produk->diskon;
                }
                
                $harga_asli = $item->harga_jual;
                $subtotal_item = $item->subtotal ?? ($item->jumlah * $harga_asli);
                
                $ada_diskon_produk = ($diskon_produk > 0);
                $harga_sebelum_diskon = $harga_asli;
                $harga_setelah_diskon = $harga_asli;
                $label_diskon = '';
                $hemat_per_item = 0;
                
                if ($ada_diskon_produk) {
                    $harga_sebelum_diskon = $harga_asli;
                    $diskon_rupiah = ($harga_asli * $diskon_produk) / 100;
                    $harga_setelah_diskon = $harga_asli - $diskon_rupiah;
                    $label_diskon = number_format($diskon_produk, 0) . '% OFF';
                    
                    $hemat_per_item = ($harga_sebelum_diskon - $harga_setelah_diskon) * $item->jumlah;
                    $total_item_discount += $hemat_per_item;
                }
                
                $subtotal_sebelum = $item->jumlah * $harga_sebelum_diskon;
                $subtotal_setelah = $item->jumlah * $harga_setelah_diskon;
            @endphp
            
            <tr>
                <td colspan="3" style="font-weight: bold;">{{ $item->produk->nama_produk }}</td>
            </tr>
            
            @if($ada_diskon_produk)
                {{-- Item dengan diskon: tampilkan harga asli dicoret --}}
                <tr>
                    <td class="price-crossed" style="width: 55%;">
                        {{ $item->jumlah }} x {{ format_uang($harga_sebelum_diskon) }}
                    </td>
                    <td class="discount-label text-center" style="width: 20%;">{{ $label_diskon }}</td>
                    <td class="text-right price-crossed" style="width: 25%;">{{ format_uang($subtotal_sebelum) }}</td>
                </tr>
                <tr>
                    <td class="price-discounted" style="width: 55%;">
                        {{ $item->jumlah }} x {{ format_uang($harga_setelah_diskon) }}
                    </td>
                    <td style="width: 20%;"></td>
                    <td class="text-right price-discounted" style="width: 25%;">{{ format_uang($subtotal_setelah) }}</td>
                </tr>
            @else
                {{-- Item tanpa diskon: tampil normal --}}
                <tr>
                    <td style="width: 75%;">{{ $item->jumlah }} x {{ format_uang($harga_asli) }}</td>
                    <td style="width: 25%;" class="text-right">{{ format_uang($subtotal_item) }}</td>
                </tr>
            @endif
        @endforeach
    </table>
    <p class="text-center">---------------------------------</p>

    <!-- Summary -->
    <table width="100%" style="border: 0;" class="total-section">
        @php
            // Hitung diskon member dari settings (di akhir transaksi)
            $diskon_member_rupiah = 0;
            $diskon_member_persen = 0;
            
            if($penjualan->diskon > 0) {
                if($penjualan->diskon < 100) {
                    $diskon_member_persen = $penjualan->diskon;
                    $diskon_member_rupiah = ($penjualan->total_harga * $diskon_member_persen) / 100;
                } else {
                    $diskon_member_rupiah = $penjualan->diskon;
                }
            }
            
            $total_hemat = $total_item_discount + $diskon_member_rupiah;
        @endphp
        
        <tr>
            <td>Subtotal:</td>
            <td class="text-right">{{ format_uang($penjualan->total_harga) }}</td>
        </tr>
        
        {{-- Tampilkan diskon item jika ada --}}
        @if($total_item_discount > 0)
            <tr>
                <td>Total Disc Item:</td>
                <td class="text-right">-{{ format_uang($total_item_discount) }}</td>
            </tr>
        @endif
        
        {{-- Diskon Member TERPISAH di akhir transaksi --}}
        @if($diskon_member_rupiah > 0)
            <tr>
                <td>
                    @if(isset($penjualan->member) && $penjualan->member)
                        Disc Member ({{ $diskon_member_persen }}%):
                    @else
                         Disc Tambahan:
                    @endif
                </td>
                <td class="text-right">-{{ format_uang($diskon_member_rupiah) }}</td>
            </tr>
        @endif
        
        <tr>
            <td> Total Item:</td>
            <td class="text-right">{{ $penjualan->total_item }} pcs</td>
        </tr>
    </table>
    
    {{-- Tampilkan total penghematan --}}
    @if($total_hemat > 0)
        <table width="100%" style="border: 0;">
            <tr class="savings-highlight">
                <td class="text-center bold" colspan="2" style="font-size: 12pt; padding: 5px 0;">
                     TOTAL HEMAT: {{ format_uang($total_hemat) }}
                </td>
            </tr>
        </table>
        <br>
    @endif
    
    <!-- Grand Total -->
    <table width="100%" style="border: 0;">
        <tr class="grand-total">
            <td class="bold" style="font-size: 13pt;"> TOTAL BAYAR:</td>
            <td class="text-right bold" style="font-size: 13pt;">{{ format_uang($penjualan->bayar) }}</td>
        </tr>
        <tr>
            <td>Tunai:</td>
            <td class="text-right">{{ format_uang($penjualan->diterima) }}</td>
        </tr>
        <tr>
            <td>Kembali:</td>
            <td class="text-right">{{ format_uang($penjualan->diterima - $penjualan->bayar) }}</td>
        </tr>
    </table>

    <!-- Footer -->
    <p class="text-center">=================================</p>
    <p class="text-center bold">🙏 TERIMA KASIH 🙏</p>
    <p class="text-center">Barang yang sudah dibeli</p>
    <p class="text-center">tidak dapat dikembalikan</p>
    
    @if($total_hemat > 0)
        <p class="text-center discount-item">
             Anda berhemat {{ format_uang($total_hemat) }} hari ini! 
        </p>
    @endif
    
    @if(isset($setting->website) || isset($setting->email))
        <br>
        <p class="text-center">
            @if(isset($setting->website))
                {{ $setting->website }}
            @endif
            @if(isset($setting->email))
                <br> {{ $setting->email }}
            @endif
        </p>
    @endif

    <script>
        let body = document.body;
        let html = document.documentElement;
        let height = Math.max(
                body.scrollHeight, body.offsetHeight,
                html.clientHeight, html.scrollHeight, html.offsetHeight
            );

        document.cookie = "innerHeight=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        document.cookie = "innerHeight="+ ((height + 50) * 0.264583);
    </script>
</body>
</html>