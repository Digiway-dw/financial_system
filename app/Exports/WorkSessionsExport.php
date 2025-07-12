<?php

namespace App\Exports;

use App\Models\Domain\Entities\WorkSession;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WorkSessionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $sessions;

    public function __construct($sessions)
    {
        $this->sessions = $sessions;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->sessions;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'User',
            'Login Time',
            'Logout Time',
            'Duration (minutes)',
            'IP Address',
            'User Agent',
        ];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->id,
            $row->user->name ?? 'Unknown User',
            $row->login_at ? $row->login_at->format('Y-m-d H:i:s') : '',
            $row->logout_at ? $row->logout_at->format('Y-m-d H:i:s') : 'Still Active',
            $row->duration_minutes ?? 'N/A',
            $row->ip_address,
            $row->user_agent,
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold
            1 => ['font' => ['bold' => true]],
        ];
    }
}
