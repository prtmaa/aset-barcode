<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;


class AssetExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithColumnFormatting, WithDrawings, WithEvents
{
    protected $assets;
    protected $kategoriIds;
    protected $tglDari;
    protected $tglSampai;

    public function __construct($kategoriIds = [], $tglDari = null, $tglSampai = null)
    {
        $this->kategoriIds = $kategoriIds;
        $this->tglDari = $tglDari;
        $this->tglSampai = $tglSampai;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('D')->getAlignment()->setWrapText(true);
    }


    public function columnWidths(): array
    {
        return [
            'A' => 3.5,
            'B' => 20,
            'C' => 30,
            'D' => 40,
            'E' => 20,
            'F' => 24,
            'G' => 7,
            'H' => 20,
            'I' => 20,
            'J' => 16,
            'K' => 16,
            'L' => 12,
            'M' => 15,
            'N' => 22,
            'O' => 12,
            'P' => 12,
            'Q' => 14,
            'R' => 22,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'J' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'K' => '#,##0',
        ];
    }



    public function collection()
    {
        $query = Asset::with([
            'lokasi',
            'tipe',
            'vendor',
            'atributValues.atribut',
            'activeAssignment.employee'
        ]);

        // ✅ FILTER KATEGORI
        if (!empty($this->kategoriIds)) {
            $query->whereIn('kategori_id', $this->kategoriIds);
        }

        // ✅ FILTER TANGGAL PEMBELIAN
        if ($this->tglDari && $this->tglSampai) {
            $query->whereBetween('tanggal_pembelian', [
                $this->tglDari,
                $this->tglSampai
            ]);
        } elseif ($this->tglDari) {
            $query->whereDate('tanggal_pembelian', '>=', $this->tglDari);
        } elseif ($this->tglSampai) {
            $query->whereDate('tanggal_pembelian', '<=', $this->tglSampai);
        }

        // SIMPAN KE PROPERTY (UNTUK DRAWINGS)
        $this->assets = $query
            ->orderBy('created_at', 'desc')
            ->get();

        $no = 1;

        return $this->assets->map(function ($asset) use (&$no) {

            $spesifikasi = $asset->atributValues
                ->map(fn($val) => $val->atribut->nama_atribut . ': ' . $val->nilai)
                ->implode("\n");

            $emp = optional($asset->activeAssignment)->employee;

            return [
                $no++,
                $asset->kode_aset,
                $asset->nama_aset,
                $spesifikasi,
                $asset->vendor->nama ?? '-',
                $asset->tipe->nama ?? '-',
                $asset->jumlah,
                $asset->lokasi->nama ?? '-',
                optional($emp)->nama ?? '-',
                $asset->tanggal_pembelian,
                $asset->harga,
                $asset->kondisi,
                $asset->status,
                '',
                '',
                '',
                '',
                '',
            ];
        });
    }


    public function headings(): array
    {
        return [
            'No',
            'Kode Aset',
            'Nama Aset',
            'Spesifikasi',
            'Nama Vendor',
            'Tipe Aset',
            'Jumlah',
            'Lokasi',
            'PIC/Divisi',
            'Tanggal Perolehan',
            'Hilai Perolehan',
            'Kondisi',
            'Status',
            'Foto',
            'Dicek Oleh',
            'Tanggal Cek',
            'Hasil Cek (✓/✗)',
            'Keterangan Tambahan',
        ];
    }

    public function drawings()
    {
        $drawings = [];
        $row = 2;

        foreach ($this->assets as $asset) {

            if ($asset->foto && file_exists(public_path($asset->foto))) {

                $drawing = new Drawing();
                $drawing->setPath(public_path($asset->foto));
                $drawing->setHeight(80);
                $drawing->setCoordinates('N' . $row);
                $drawing->setOffsetX(10);
                $drawing->setOffsetY(5);
                $drawing->setHeight(70);


                $drawings[] = $drawing;
            }

            $row++;
        }

        return $drawings;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // BORDER SEMUA DATA (A sampai N)
                $sheet->getStyle("A1:R{$lastRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                // TINGGI BARIS
                for ($row = 2; $row <= $lastRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(90);
                }


                // HEADER
                $sheet->getStyle('A1:R1')->getFont()->setBold(true);
                $sheet->freezePane('A2');

                // HEADER CENTER
                $sheet->getStyle('A1:R1')
                    ->getAlignment()
                    ->setHorizontal('center')
                    ->setVertical('center');

                // DATA CENTER (KOLOM TERTENTU)
                $centerCols = ['A', 'G', 'N'];

                foreach ($centerCols as $col) {
                    $sheet->getStyle("{$col}2:{$col}{$lastRow}")
                        ->getAlignment()
                        ->setHorizontal('center')
                        ->setVertical('center');
                }

                $leftCols = ['B', 'C', 'D', 'E', 'F', 'H', 'I', 'J', 'K', 'L', 'M'];

                foreach ($leftCols as $col) {
                    $sheet->getStyle("{$col}2:{$col}{$lastRow}")
                        ->getAlignment()
                        ->setHorizontal('left')
                        ->setVertical('center');
                }
            },
        ];
    }
}
