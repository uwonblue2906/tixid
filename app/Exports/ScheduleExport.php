<?php

namespace App\Exports;

use App\Models\Schedule;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ScheduleExport implements FromCollection, WithHeadings, WithMapping
{
    private $number = 0;

    public function collection()
    {
        return Schedule::all();
    }
    public function headings(): array
    {
        return [
            'No',
            'Nama Bioskop',
            'Judul Film',
            'Harga',
            'Jam Tayang',
        ];
    }

    public function map($schedule): array
    {
        return [
            ++$this->number,
            $schedule->cinema ? $schedule->cinema->name : '-',
            $schedule->movie ? $schedule->movie->title : '-',
            'Rp. ' . number_format($schedule->price, 0, ',', '.'),
            implode(", ", $schedule->hours),

        ];
    }
}
