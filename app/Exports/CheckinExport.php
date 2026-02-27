<?php

namespace App\Exports;

use App\Models\CheckinLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CheckinExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return CheckinLog::with(['user', 'membership.package', 'staff'])
                ->orderBy('checkin_time', 'desc')
                ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Member',
            'Email Member',
            'Paket Membership',
            'Waktu Check-in',
            'Waktu Check-out',
            'Durasi (menit)',
            'Staff',
            'Catatan'
        ];
    }

    public function map($checkin): array
    {
        $duration = null;
        if ($checkin->checkout_time) {
            $duration = $checkin->checkin_time->diffInMinutes($checkin->checkout_time);
        }

        return [
            $checkin->id,
            $checkin->user->name,
            $checkin->user->email,
            $checkin->membership->package->name ?? '-',
            $checkin->checkin_time->format('d/m/Y H:i'),
            $checkin->checkout_time ? $checkin->checkout_time->format('d/m/Y H:i') : 'Belum checkout',
            $duration ?? '-',
            $checkin->staff ? $checkin->staff->name : 'Mandiri',
            $checkin->notes ?? '-'
        ];
    }
}
