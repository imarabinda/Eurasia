<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class FabricsExport implements FromCollection,WithStyles,WithMapping,WithHeadings,WithProperties,ShouldAutoSize,WithTitle,WithCustomCsvSettings
{

    protected $ids = [];
    protected $for = 'Fabrics';
    protected $title = 'Fabrics export';

    public function __construct($ids = [],$title = null){
        $this->ids = $ids;
        if($title){
            $this->title=$title;
        }
    }

 
     public function collection()
    {
        $model= \App\Models\Fabric::with('fabric_type','fabric_color');

        $model->withSum('quantity_used','quantity');

        if(count($this->ids) > 0){
            $model->whereIn('id',$this->ids);
        }
        $items = $model->get();
        
        return $items;
    }

    
     public function headings(): array{     
        return [
           'Receive Date', 'Mill ID','Mill Ref ID','Fabric Type','Fabric Color','Width','Total Quantity','Used Quantity','Remaining Quantity','Created Date','Last Updated Date'
        ];
    }

    public function map($item): array{
        return [
            $item->receiving_date,
            $item->mill_id,
            $item->mill_ref_id,
            $item->fabric_type_name,
            $item->fabric_color_name,
            $item->width,
            $item->total_quantity,
            $item->quantity_used_sum_quantity ?: '0',
            $item->total_quantity-$item->quantity_used_sum_quantity ?:'0',
            $item->created_at,
            $item->updated_at,
        ];
    }



     public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],

          ];
    }

    public function title(): string
    {
        return $this->title;
    }


     public function properties(): array
    {
        $user = auth()->user()->first_name.' '.auth()->user()->last_name;
        return [
            'creator'        => $user,
            'lastModifiedBy' => $user,
            'title'          => $this->title,
            'description'    => 'Latest '.$this->for,
            'subject'        => $this->for,
            'keywords'       => $this->for.',export,spreadsheet',
            'category'       => $this->for,
            'manager'        => $user,
            'company'        => env('APP_NAME'),
        ];
    }


     public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';'
        ];
    }

}
