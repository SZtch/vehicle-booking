<?php

namespace App\Exports;

use App\Models\Booking;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class BookingsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(
        private ?string $dateFrom = null,
        private ?string $dateTo = null,
        private ?string $status = null
    ) {}

    public function query()
    {
        $query = Booking::with(['vehicle', 'driver', 'admin', 'approvals.approver'])
            ->latest();

        if ($this->dateFrom) {
            $query->whereDate('start_date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('start_date', '<=', $this->dateTo);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Kode Booking',
            'Kendaraan',
            'Plat Nomor',
            'Tipe Kendaraan',
            'Driver',
            'Tujuan',
            'Asal',
            'Destinasi',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Jumlah Penumpang',
            'Status',
            'Diajukan Oleh',
            'Approver L1',
            'Status L1',
            'Approver L2',
            'Status L2',
            'Dibuat Pada',
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
            $booking->start_date?->format('d/m/Y H:i'),
            $booking->end_date?->format('d/m/Y H:i'),
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

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1e3a5f'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function title(): string
    {
        return 'Laporan Pemesanan';
    }
}
