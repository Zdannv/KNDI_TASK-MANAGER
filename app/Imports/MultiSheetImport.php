<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiSheetImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'users' => new UsersImport(),
            'clients' => new ClientsImport(),
            'projects' => new ProjectsImport(),
            'tasks' => new TasksImport(),
        ];
    }
}
