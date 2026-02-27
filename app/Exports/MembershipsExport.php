<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MembershipsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $memberships;

    public function __construct(Collection $memberships)
    {
        $this->memberships = $memberships;
    }

    public function collection()
    {
        return $this->memberships;
    }

    public function headings(): array
    {
        return [
            '#',
            'Nama Member',
            'Email',
            'Paket',
            'Status',
            'Tanggal Aktivasi',
        ];
    }

    public function map($membership): array
    {
        static $number = 0;
        $number++;
        return [
            $number,
            $membership->user->name ?? '-',
            $membership->user->email ?? '-',
            $membership->package->name ?? '-', // Asumsi ada relasi 'package'
            ucfirst($membership->status),
            $membership->created_at->format('d F Y, H:i'),
        ];
    }
}