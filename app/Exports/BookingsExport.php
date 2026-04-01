<?php

namespace App\Exports;

use App\Models\Booking;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class BookingsExport implements FromQuery, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithEvents
{
    public function __construct(
        private ?string $dateFrom = null,
        private ?string $dateTo = null,
        private ?string $status = null
    ) {}

    public function query()
    {
        $query = Booking::with(['vehicle', 'driver', 'admin', 'approvals.approver'])->latest();

        if ($this->dateFrom) $query->whereDate('start_date', '>=', $this->dateFrom);
        if ($this->dateTo)   $query->whereDate('start_date', '<=', $this->dateTo);
        if ($this->status)   $query->where('status', $this->status);

        return $query;
    }

    public function headings(): array
    {
        return [
            'Kode Booking', 'Kendaraan', 'Plat Nomor', 'Tipe Kendaraan',
            'Driver', 'Keperluan', 'Asal', 'Destinasi',
            'Tanggal Mulai', 'Tanggal Selesai', 'Penumpang', 'Status',
            'Diajukan Oleh', 'Approver L1', 'Status L1', 'Approver L2', 'Status L2', 'Dibuat Pada',
        ];
    }

    public function map($booking): array
    {
        $l1 = $booking->approvals->where('level', 1)->first();
        $l2 = $booking->approvals->where('level', 2)->first();

        return [
            $booking->booking_code,
            $booking->vehicle?->name,
            $booking->vehicle?->plate_number,
            $booking->vehicle?->getTypeLabel(),
            $booking->driver?->name,
            $booking->purpose,
            $booking->origin,
            $booking->destination,
            $booking->start_date?->format('d/m/Y'),
            $booking->end_date?->format('d/m/Y'),
            $booking->passenger_count,
            $booking->getStatusLabel(),
            $booking->admin?->name,
            $l1?->approver?->name ?? '-',
            $l1 ? ucfirst($l1->status) : '-',
            $l2?->approver?->name ?? '-',
            $l2 ? ucfirst($l2->status) : '-',
            $booking->created_at?->format('d/m/Y H:i'),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Ambil lastRow SEBELUM insert (row 1 = header, row 2+ = data)
                $lastRow = $sheet->getHighestRow();
                $lastCol = 'R'; // 18 kolom (A–R)

                // --- Insert 2 baris di atas untuk judul ---
                $sheet->insertNewRowBefore(1, 2);
                // Setelah insert: row 1 = judul, row 2 = subtitle, row 3 = header, row 4+ = data

                $headerRow  = 3;
                $dataStart  = 4;
                $dataEnd    = $lastRow + 2; // geser 2 karena insert

                // --- Row 1: Judul ---
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->setCellValue('A1', 'LAPORAN PEMESANAN KENDARAAN');
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14, 'name' => 'Arial', 'color' => ['rgb' => '1e3a5f']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(30);

                // --- Row 2: Tanggal cetak ---
                $sheet->mergeCells("A2:{$lastCol}2");
                $sheet->setCellValue('A2', 'Dicetak pada: ' . now()->format('d/m/Y H:i'));
                $sheet->getStyle('A2')->applyFromArray([
                    'font'      => ['italic' => true, 'size' => 10, 'name' => 'Arial', 'color' => ['rgb' => '666666']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(18);

                // --- Row 3: Header ---
                $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11, 'name' => 'Arial'],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension($headerRow)->setRowHeight(22);

                // --- Zebra stripe pada baris data ---
                for ($row = $dataStart; $row <= $dataEnd; $row++) {
                    $color = ($row % 2 === 0) ? 'F2F7FF' : 'FFFFFF';
                    $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]],
                        'font'      => ['name' => 'Arial', 'size' => 10],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                }

                // --- Border seluruh tabel (header + data) ---
                $sheet->getStyle("A{$headerRow}:{$lastCol}{$dataEnd}")->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN,   'color' => ['rgb' => 'CCCCCC']],
                        'outline'    => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '1e3a5f']],
                    ],
                ]);

                // --- Kolom tertentu rata tengah ---
                foreach (['C', 'I', 'J', 'K', 'O', 'Q', 'R'] as $col) {
                    $sheet->getStyle("{$col}{$dataStart}:{$col}{$dataEnd}")
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // --- Freeze pane di bawah header ---
                $sheet->freezePane("A{$dataStart}");
            },
        ];
    }

    public function title(): string
    {
        return 'Laporan Pemesanan';
    }
}
