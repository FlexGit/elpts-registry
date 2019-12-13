<?php

namespace App;

use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class Export implements FromArray, WithColumnFormatting
{
    private $data;
	
    public function __construct($data)
    {
        $this->data = $data;
    }
	
	public function array(): array
	{
		return $this->data;
	}
	
	/**
     * @return array
     */
    public function columnFormats(): array
    {
		return [
			'E' => NumberFormat::FORMAT_NUMBER,
			'F' => NumberFormat::FORMAT_NUMBER,
			'G' => NumberFormat::FORMAT_NUMBER,
		];
    }
}
