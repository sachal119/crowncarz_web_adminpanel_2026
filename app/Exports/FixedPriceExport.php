<?php

namespace App\Exports;

use App\Models\FixedPrice;
use Maatwebsite\Excel\Concerns\FromCollection;

class FixedPriceExport implements FromCollection
{
    public function collection()
    {
        return FixedPrice::all();
    }
}
