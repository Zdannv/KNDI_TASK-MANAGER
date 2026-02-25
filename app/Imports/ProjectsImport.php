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
            'projectOwner_id' => 'required|numeric',
            'name' => 'required|string|max:255',
        ];
    }

    public function model(array $row)
    {
        $owner = ProjectOwner::where('name', $row['projectOwner_name'])->first();

        if (!$owner) {
            throw new \Exception("Project Owner Not Found: " . $row['projectOwner_name']);
        }

        return new Project([
            'projectOwner_id' => $owner->id,
            'name' => $row['name'],
            'creator' => Auth::id(),
            'updater' => Auth::id(),
        ]);
    }
}
