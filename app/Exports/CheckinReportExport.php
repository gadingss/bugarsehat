<?php

namespace App\Exports;

use App\Models\CheckinLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class CheckinReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    protected $checkinLogs;

    public function __construct($checkinLogs)
    {
        $this->checkinLogs = $checkinLogs;
    }

    public function collection()
    {
        return $this->checkinLogs;
    }

    public function title(): string
    {
        return 'Laporan Check-in';
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tanggal Check-in',
            'Nama Member',
            'Email Member',
            'Paket Membership',
            'Staff',
            'Jam Check-in',
            'Jam Checkout',
            'Durasi',
            'Status',
            'Catatan'
        ];
    }

    public function map($checkinLog): array
    {
        try {
            return [
                $checkinLog->id ?? '',
                $checkinLog->checkin_time ? $checkinLog->checkin_time->format('d/m/Y') : '-',
                $checkinLog->user ? ($checkinLog->user->name ?? 'Walk-in') : 'Walk-in',
                $checkinLog->user ? ($checkinLog->user->email ?? '-') : '-',
                $checkinLog->membership ? ($checkinLog->membership->membershipPacket->name ?? 'Non-member') : 'Non-member',
                $checkinLog->staff ? ($checkinLog->staff->name ?? '-') : '-',
                $checkinLog->checkin_time ? $checkinLog->checkin_time->format('H:i') : '-',
                $checkinLog->checkout_time ? $checkinLog->checkout_time->format('H:i') : 'Belum checkout',
                $checkinLog->getFormattedDuration(),
                $checkinLog->isActive() ? 'Aktif' : 'Selesai',
                $checkinLog->notes ?? '-'
            ];
        } catch (\Exception $e) {
            return [
                $checkinLog->id ?? '',
                '-',
                'Error',
                'Error',
                'Error',
                '-',
                '-',
                '-',
                '-',
                'ERROR',
                '-'
            ];
        }
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
