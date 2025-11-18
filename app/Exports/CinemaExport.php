<?php

namespace App\Exports;

use App\Models\Cinema;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CinemaExport implements FromCollection, WithHeadings, WithMapping
{
    private $number = 0;

    public function collection()
    {
        return Cinema::all();
    }

    public function map($cinema): array
    {
        return [
            ++$this->number,
            $cinema->name,
            $cinema->location,
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Bioskop',
            'Lokasi',
        ];
    }
}
