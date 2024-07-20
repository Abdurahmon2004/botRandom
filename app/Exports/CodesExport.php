<?php

namespace App\Exports;

use App\Models\Code;
use App\Models\Group;
use Maatwebsite\Excel\Concerns\FromCollection;

class CodesExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $groupId;

    public function __construct($groupId)
    {
        $this->groupId = $groupId;
    }
    public function collection()
    {
        return Code::with('group')
                    ->where('group_id', $this->groupId)
                    ->get()
                    ->map(function ($code) {
                        return [
                            'code' => $code->code,
                            'group' => $code->group->name,
                        ];
                    });
    }

    public function headings(): array
    {
        return [
            'Code',
            'Group Name'
        ];
    }
}
