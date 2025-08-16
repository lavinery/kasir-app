<?php

namespace App\Exports;

use App\Models\Produk;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ProdukExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $selectedIds;
    protected $qty;

    public function __construct($selectedIds, $qty = 1)
    {
        $this->selectedIds = $selectedIds;
        $this->qty = $qty;
    }

    public function collection()
    {
        return Produk::leftJoin('kategori', 'kategori.id_kategori', 'produk.id_kategori')
            ->select('produk.*', 'nama_kategori')
            ->whereIn('produk.id_produk', $this->selectedIds)
            ->orderBy('produk.nama_produk', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Produk',
            'Nama Produk',
            'Kategori',
            'Merk',
            'Harga Beli',
            'Harga Jual',
            'Keuntungan',
            'Diskon (%)',
            'Stok',
            'Status',
            'Qty'
        ];
    }

    public function map($produk): array
    {
        static $no = 1;

        return [
            $no++,
            $produk->kode_produk,
            $produk->nama_produk,
            $produk->nama_kategori ?? '-',
            $produk->merk ?? '-',
            'Rp ' . number_format($produk->harga_beli, 0, ',', '.'),
            'Rp ' . number_format($produk->harga_jual, 0, ',', '.'),
            'Rp ' . number_format($produk->keuntungan, 0, ',', '.'),
            $produk->diskon . '%',
            number_format($produk->stok, 0, ',', '.'),
            $produk->stok > 0 ? 'Tersedia' : 'Kosong',
            $this->qty
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:L1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        $lastRow = $sheet->getHighestRow();

        $sheet->getStyle('A2:L' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        $sheet->getStyle('F2:J' . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT
            ]
        ]);

        $sheet->getStyle('K2:K' . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);

        $sheet->getStyle('L2:L' . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);

        for ($i = 1; $i <= $lastRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(-1);
        }

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 15,
            'C' => 30,
            'D' => 15,
            'E' => 15,
            'F' => 15,
            'G' => 15,
            'H' => 15,
            'I' => 10,
            'J' => 10,
            'K' => 12,
            'L' => 8
        ];
    }

    public function title(): string
    {
        return 'Data Produk';
    }
}
