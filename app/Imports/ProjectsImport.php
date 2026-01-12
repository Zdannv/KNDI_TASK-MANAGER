<?php

namespace App\Imports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\Auth;

class ProjectsImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading, SkipsEmptyRows
{
    public function chunkSize(): int
    {
        return 1000;
    }
    
    public function rules(): array
    {
        return [
            'client_id' => 'required|numeric',
            'name' => 'required|string|max:255',
        ];
    }

    public function model(array $row)
    {
        return new Project([
            'client_id' => $row['client_id'],
            'name' => $row['name'],
            'creator' => Auth::id(),
            'updater' => Auth::id(),
        ]);
    }
}
