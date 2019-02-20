<?php

namespace App;

use Illuminate\Contracts\View\View;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class Export implements FromView, ShouldAutoSize, WithColumnFormatting
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('exports.xlsx', [
            'data' => $this->data
        ]);
    }

	/**
     * @return array
     */
    public function columnFormats(): array
    {

		$arr = [];

		foreach (range('A','Z') as $v)
		{
		}

        return $arr;
    }
}