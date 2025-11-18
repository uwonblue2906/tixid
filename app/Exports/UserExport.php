<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserExport implements FromCollection, WithHeadings, WithMapping
{
    private $number = 0;

    public function collection()
    {
        // ambil semua user, urutkan berdasarkan role
        return User::orderBy('role')->get();
    }

    public function map($user): array
    {
        return [
            ++$this->number,
            $user->name,
            $user->email,
            $user->role,
            $user->created_at->format('d-m-Y'), // Tanggal bergabung
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'Email',
            'Role',
            'Tanggal Bergabung',
        ];
    }
}
