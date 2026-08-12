<?php

namespace App\Exports;

use App\Models\DataKontrak;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithProperties;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DataKontrakFullExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnWidths,
    WithTitle,
    WithProperties
{
    protected int $rowCount = 0;

    public function title(): string
    {
        return 'Data Kontrak';
    }

    public function properties(): array
    {
        return [
            'creator'        => 'Apyrent System',
            'lastModifiedBy' => 'Apyrent System',
            'title'          => 'Export Data Kontrak',
            'description'    => 'Data lengkap kontrak kendaraan beserta asuransi dan lampiran',
            'subject'        => 'Data Kontrak',
            'keywords'       => 'kontrak,kendaraan,asuransi,cicilan',
            'category'       => 'Export',
        ];
    }

    public function collection()
    {
        return DataKontrak::with(['kendaraan', 'attachments', 'leasings'])
            ->orderBy('serial_number')
            ->get();
    }

    public function headings(): array
    {
        return [
            // ── Identitas ─────────────────────────────────
            'No',                    // A
            'Serial Number',         // B
            'No Kontrak',            // C
            // ── Kendaraan ─────────────────────────────────
            'Mobil / Merk',          // D
            'Tahun',                 // E
            'Nopol',                 // F
            'User / Customer',       // G
            // ── Cicilan ───────────────────────────────────
            'Angsuran / Bulan (Rp)', // H
            'Jatuh Tempo',           // I
            'Periode Mulai',         // J
            'Periode Selesai',       // K
            'Jumlah Cicilan',        // L
            'Cicilan Tersisa',       // M
            'Sudah Terbayar',        // N
            'Total Nilai (Rp)',      // O
            'Sisa Nilai (Rp)',       // P
            'Status Cicilan',        // Q
            // ── Pembayaran ────────────────────────────────
            'Personal Account',      // R
            'Sumber Dana Debit',     // S
            'Cara Bayar',            // T
            // ── Asuransi ──────────────────────────────────
            'Nama Asuransi',         // U
            'Alamat Asuransi',       // V
            'Nama Marketing',        // W
            'Kontak Marketing',      // X
            'Nama Bengkel',          // Y
            'Kontak Bengkel',        // Z
            // ── Dokumen ───────────────────────────────────
            'File Bukti',            // AA
            'Jumlah Lampiran',       // AB
            'Nama Lampiran',         // AC
            // ── Leasing terhubung ─────────────────────────
            'Jumlah Data Leasing',   // AD
            // ── Metadata ──────────────────────────────────
            'Dibuat Pada',           // AE
            'Diupdate Pada',         // AF
        ];
    }

    public function map($row): array
    {
        $this->rowCount++;

        $jumlah   = $row->jumlah_cicilan;
        $tersisa  = $row->cicilan_tersisa;
        $terbayar = max(0, $jumlah - $tersisa);

        // Attachments — gabungkan nama file dengan koma
        $attNames = $row->attachments->pluck('file_name')->implode(', ');
        $attCount = $row->attachments->count();

        // Bukti — ambil nama file saja
        $buktiFn = $row->bukti ? basename($row->bukti) : '-';

        return [
            // Identitas
            $this->rowCount,
            $row->serial_number ?? '-',
            $row->no_kontrak    ?? '-',
            // Kendaraan
            $row->mobil         ?? '-',
            $row->tahun         ?? '-',
            $row->nopol         ?? '-',
            $row->user_kontrak  ?? '-',
            // Cicilan
            $row->angsuran_per_bulan ?? 0,
            $row->jatuh_tempo ? 'Tgl ' . $row->jatuh_tempo : '-',
            $row->periode_mulai   ? $row->periode_mulai->format('d/m/Y')   : '-',
            $row->periode_selesai ? $row->periode_selesai->format('d/m/Y') : '-',
            $jumlah,
            $tersisa,
            $terbayar,
            $jumlah   * ($row->angsuran_per_bulan ?? 0),
            $tersisa  * ($row->angsuran_per_bulan ?? 0),
            $row->status_cicilan,
            // Pembayaran
            $row->personal_account  ?? '-',
            $row->sumber_dana_debit ?? '-',
            $row->cara_bayar        ?? '-',
            // Asuransi
            $row->nama_asuransi    ?? '-',
            $row->alamat_asuransi  ?? '-',
            $row->nama_marketing   ?? '-',
            $row->kontak_marketing ?? '-',
            $row->nama_bengkel     ?? '-',
            $row->kontak_bengkel   ?? '-',
            // Dokumen
            $buktiFn,
            $attCount,
            $attCount > 0 ? $attNames : '-',
            // Leasing
            $row->leasings->count(),
            // Metadata
            $row->created_at?->format('d/m/Y H:i') ?? '-',
            $row->updated_at?->format('d/m/Y H:i') ?? '-',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A'  => 5,   // No
            'B'  => 20,  // Serial Number
            'C'  => 22,  // No Kontrak
            'D'  => 22,  // Mobil
            'E'  => 8,   // Tahun
            'F'  => 14,  // Nopol
            'G'  => 28,  // User
            'H'  => 22,  // Angsuran
            'I'  => 12,  // Jatuh Tempo
            'J'  => 14,  // Periode Mulai
            'K'  => 14,  // Periode Selesai
            'L'  => 16,  // Jumlah Cicilan
            'M'  => 16,  // Cicilan Tersisa
            'N'  => 16,  // Sudah Terbayar
            'O'  => 22,  // Total Nilai
            'P'  => 22,  // Sisa Nilai
            'Q'  => 16,  // Status
            'R'  => 24,  // Personal Account
            'S'  => 24,  // Sumber Dana
            'T'  => 16,  // Cara Bayar
            'U'  => 26,  // Nama Asuransi
            'V'  => 36,  // Alamat Asuransi
            'W'  => 22,  // Nama Marketing
            'X'  => 18,  // Kontak Marketing
            'Y'  => 22,  // Nama Bengkel
            'Z'  => 18,  // Kontak Bengkel
            'AA' => 30,  // File Bukti
            'AB' => 14,  // Jumlah Lampiran
            'AC' => 40,  // Nama Lampiran
            'AD' => 18,  // Jumlah Data Leasing
            'AE' => 18,  // Dibuat Pada
            'AF' => 18,  // Diupdate Pada
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $this->rowCount + 1;
        $lastCol = 'AF';

        // ── Header row ──────────────────────────────────────────────────
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 10,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4338CA'],  // indigo
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'FFFFFF'],
                ],
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(36);

        // ── Section color groups di header ────────────────────────────
        // Kendaraan (D–G) — biru
        $sheet->getStyle('D1:G1')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']],
        ]);
        // Cicilan (H–Q) — teal
        $sheet->getStyle('H1:Q1')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F766E']],
        ]);
        // Pembayaran (R–T) — amber
        $sheet->getStyle('R1:T1')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'B45309']],
        ]);
        // Asuransi (U–Z) — violet
        $sheet->getStyle('U1:Z1')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '7C3AED']],
        ]);
        // Dokumen (AA–AD) — rose
        $sheet->getStyle('AA1:AD1')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'BE185D']],
        ]);
        // Metadata (AE–AF) — slate
        $sheet->getStyle('AE1:AF1')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '475569']],
        ]);

        // ── Freeze pane ──────────────────────────────────────────────────
        $sheet->freezePane('A2');

        // ── Data rows ────────────────────────────────────────────────────
        if ($lastRow >= 2) {
            $sheet->getStyle("A2:{$lastCol}{$lastRow}")->applyFromArray([
                'font'      => ['size' => 9],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => false,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'E5E7EB'],
                    ],
                ],
            ]);

            // Zebra striping
            for ($r = 2; $r <= $lastRow; $r++) {
                if ($r % 2 === 0) {
                    $sheet->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray([
                        'fill' => [
                            'fillType'   => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F5F3FF'],  // lavender muda
                        ],
                    ]);
                }
            }

            // Right-align kolom angka
            foreach (['H', 'L', 'M', 'N', 'O', 'P', 'AB', 'AD'] as $col) {
                $sheet->getStyle("{$col}2:{$col}{$lastRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }

            // Format Rp untuk kolom angsuran, total nilai, sisa nilai
            foreach (['H', 'O', 'P'] as $col) {
                $sheet->getStyle("{$col}2:{$col}{$lastRow}")
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            }

            // Center-align kolom pendek
            foreach (['A', 'E', 'I', 'L', 'M', 'N', 'AB', 'AD'] as $col) {
                $sheet->getStyle("{$col}2:{$col}{$lastRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            // Wrap text kolom teks panjang
            foreach (['V', 'AC'] as $col) {
                $sheet->getStyle("{$col}2:{$col}{$lastRow}")
                    ->getAlignment()
                    ->setWrapText(true);
            }

            // Warna status cicilan
            for ($r = 2; $r <= $lastRow; $r++) {
                $status = $sheet->getCell("Q{$r}")->getValue();
                $color  = match ($status) {
                    'Lunas'       => 'D1FAE5',
                    'Partial'     => 'FEF3C7',
                    'Belum Mulai' => 'DBEAFE',
                    default       => 'F3F4F6',
                };
                $sheet->getStyle("Q{$r}")->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $color],
                    ],
                    'font'      => ['bold' => true, 'size' => 9],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
            }
        }

        return [];
    }
}
