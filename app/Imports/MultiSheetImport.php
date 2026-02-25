<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiSheetImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'users' => new UsersImport(),
            'projectOwners' => new ProjectOwnersImport(),
            'projects' => new ProjectsImport(),
            'tasks' => new TasksImport(),
        ];
    }
}
