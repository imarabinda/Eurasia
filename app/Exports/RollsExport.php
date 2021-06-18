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


class RollsExport implements FromCollection,WithStyles,WithMapping,WithHeadings,WithProperties,ShouldAutoSize,WithTitle,WithCustomCsvSettings
{

    protected $ids = [];
    protected $for = 'Rolls';
    protected $title = 'Rolls export';
    protected $fabric = null;

    public function __construct($ids = [],$fabric = null,$title = null){
        $this->ids = $ids;
        $this->fabric = $fabric;
        if($title){
            $this->title=$title;
        }
    }

 
     public function collection()
    {
        $model= \App\Models\FabricRoll::where('fabric_id',$this->fabric)->withSum('quantity_used_logs','quantity');

        if(count($this->ids) > 0){
            $model->whereIn('id',$this->ids);
        }
        $items = $model->get();
        
        return $items;
    }

    
     public function headings(): array{     
        return [
           'Name', 'Quantity','Used Quantity','Remaining Quantity','Created Date','Last Updated Date'
        ];
    }

    public function map($item): array{
        return [
            $item->name,
            $item->quantity ?: '0',
            $item->quantity_used_logs_sum_quantity ?:'0',
            $item->quantity-$item->quantity_used_logs_sum_quantity ?:'0',
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
