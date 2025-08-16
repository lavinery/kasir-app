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
            font-size: 10pt;
            color: #555;
        }
        .savings-highlight {
            background-color: #f0f0f0;
            padding: 2px 0;
            font-weight: bold;
        }
        .price-crossed {
            text-decoration: line-through;
            color: #888;
            font-size: 10pt;
        }
        .price-discounted {
            color: #000;
            font-weight: bold;
            font-size: 11pt;
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
    
    <!-- Items dengan harga normal dicoret dan harga diskon -->
    <table width="100%" style="border: 0;">
        @php
            $total_item_discount = 0;
            $subtotal_sebelum_diskon = 0;
            $total_diskon_member = 0;
            
            // Hitung diskon member dari field diskon penjualan
            if($penjualan->diskon > 0) {
                if($penjualan->diskon < 100) {
                    $total_diskon_member = ($penjualan->total_harga * $penjualan->diskon) / 100;
                } else {
                    $total_diskon_member = $penjualan->diskon;
                }
            }
        @endphp
        
        @foreach ($detail as $item)
            @php
                // Data dari database
                $harga_jual = $item->harga_jual;
                $subtotal_item = $item->subtotal ?? ($item->jumlah * $harga_jual);
                $diskon_item = $item->diskon ?? 0;
                
                // Variabel untuk tampilan
                $ada_diskon_item = false;
                $harga_normal = $harga_jual;
                $harga_setelah_diskon = $harga_jual;
                $persen_diskon_item = 0;
                
                // 1. Cek diskon per item terlebih dahulu
                if ($diskon_item > 0) {
                    $ada_diskon_item = true;
                    
                    if ($diskon_item < 100) {
                        // Diskon item dalam persen
                        $persen_diskon_item = $diskon_item;
                        $harga_normal = $harga_jual / (1 - ($diskon_item / 100));
                        $harga_setelah_diskon = $harga_jual;
                    } else {
                        // Diskon item dalam rupiah
                        $diskon_per_unit = $diskon_item / $item->jumlah;
                        $harga_normal = $harga_jual + $diskon_per_unit;
                        $harga_setelah_diskon = $harga_jual;
                        $persen_diskon_item = ($diskon_per_unit / $harga_normal) * 100;
                    }
                    
                    $total_item_discount += ($harga_normal - $harga_setelah_diskon) * $item->jumlah;
                }
                
                // 2. Jika tidak ada diskon item tapi ada diskon member, tampilkan efeknya
                if (!$ada_diskon_item && $total_diskon_member > 0) {
                    // Hitung proporsi diskon member untuk item ini
                    $proporsi = ($item->jumlah * $harga_jual) / $penjualan->total_harga;
                    $diskon_member_item = $total_diskon_member * $proporsi;
                    $diskon_member_per_unit = $diskon_member_item / $item->jumlah;
                    
                    // Harga normal adalah harga di database, setelah diskon adalah harga final
                    $harga_normal = $harga_jual;
                    $harga_setelah_diskon = $harga_jual - $diskon_member_per_unit;
                    $ada_diskon_item = true; // Set true untuk menampilkan coretan
                }
                
                $subtotal_normal = $item->jumlah * $harga_normal;
                $subtotal_final = $item->jumlah * $harga_setelah_diskon;
                $subtotal_sebelum_diskon += $subtotal_normal;
            @endphp
            
            <tr>
                <td colspan="3">{{ $item->produk->nama_produk }}</td>
            </tr>
            
            @if($ada_diskon_item && abs($harga_normal - $harga_setelah_diskon) > 10)
                {{-- Tampilan dengan harga dicoret --}}
                <tr>
                    <td class="price-crossed">{{ $item->jumlah }} x {{ format_uang($harga_normal) }}</td>
                    <td></td>
                    <td class="text-right price-crossed">{{ format_uang($subtotal_normal) }}</td>
                </tr>
                <tr>
                    <td class="price-discounted">{{ $item->jumlah }} x {{ format_uang($harga_setelah_diskon) }}</td>
                    <td class="discount-item">
                        @if($diskon_item > 0)
                            {{-- Diskon per item: tampilkan persen --}}
                            ({{ number_format($persen_diskon_item, 0) }}% OFF)
                        @else
                            {{-- Diskon member: tidak tampilkan persen --}}
                            (DISC)
                        @endif
                    </td>
                    <td class="text-right price-discounted">{{ format_uang($subtotal_final) }}</td>
                </tr>
            @else
                {{-- Tampilan normal tanpa diskon --}}
                <tr>
                    <td>{{ $item->jumlah }} x {{ format_uang($harga_jual) }}</td>
                    <td></td>
                    <td class="text-right">{{ format_uang($subtotal_item) }}</td>
                </tr>
            @endif
        @endforeach
    </table>
    <p class="text-center">---------------------------------</p>

    <!-- Summary -->
    <table width="100%" style="border: 0;" class="total-section">
        @php
            // Hitung subtotal sebelum semua diskon
            $subtotal_asli = $subtotal_sebelum_diskon > 0 ? $subtotal_sebelum_diskon : $penjualan->total_harga;
        @endphp
        
        @if($subtotal_asli > $penjualan->total_harga || $total_item_discount > 0)
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">{{ format_uang($subtotal_asli) }}</td>
            </tr>
        @else
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">{{ format_uang($penjualan->total_harga) }}</td>
            </tr>
        @endif
        
        @if($penjualan->diskon > 0)
            @php
                // Deteksi apakah diskon dalam % atau rupiah
                $diskon_persen = 0;
                $diskon_rupiah = $penjualan->diskon;
                
                // Jika diskon kecil (< 100), anggap sebagai persen
                if($penjualan->diskon < 100) {
                    $diskon_persen = $penjualan->diskon;
                    $diskon_rupiah = ($penjualan->total_harga * $diskon_persen) / 100;
                }
            @endphp
            <tr>
                <td>
                    @if(isset($penjualan->member) && $penjualan->member)
                        Potongan Member:
                    @else
                        Total Diskon:
                    @endif
                </td>
                <td class="text-right">-{{ format_uang($diskon_rupiah) }}</td>
            </tr>
        @endif
        
        <tr>
            <td>Total Item:</td>
            <td class="text-right">{{ $penjualan->total_item }} pcs</td>
        </tr>
    </table>
    
    {{-- Tampilkan total penghematan jika ada diskon --}}
    @php
        $total_savings = $total_item_discount;
        if($penjualan->diskon > 0) {
            if($penjualan->diskon < 100) {
                $total_savings += ($penjualan->total_harga * $penjualan->diskon) / 100;
            } else {
                $total_savings += $penjualan->diskon;
            }
        }
        
        // Alternatif: hitung dari selisih subtotal dan total bayar
        if($total_savings == 0 && isset($subtotal_asli)) {
            $total_savings = $subtotal_asli - $penjualan->bayar;
        }
    @endphp
    
    @if($total_savings > 0)
        <table width="100%" style="border: 0;">
            <tr class="savings-highlight">
                <td class="text-center" colspan="2">*** ANDA TELAH HEMAT: {{ format_uang($total_savings) }} ***</td>
            </tr>
        </table>
        <br>
    @endif
    
    <!-- Grand Total -->
    <table width="100%" style="border: 0;">
        <tr class="grand-total">
            <td class="bold">TOTAL BAYAR:</td>
            <td class="text-right bold">{{ format_uang($penjualan->bayar) }}</td>
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
    <p class="text-center bold">-- TERIMA KASIH --</p>
    <p class="text-center">Barang yang sudah dibeli</p>
    <p class="text-center">tidak dapat dikembalikan</p>
    
    @if(isset($setting->website) || isset($setting->email))
        <br>
        <p class="text-center">
            @if(isset($setting->website))
                {{ $setting->website }}
            @endif
            @if(isset($setting->email))
                <br>{{ $setting->email }}
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