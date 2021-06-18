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


class RatesExport implements FromCollection,WithStyles,WithMapping,WithHeadings,WithProperties,ShouldAutoSize,WithTitle,WithCustomCsvSettings
{

    protected $ids = [];
    protected $for = 'Rates';
    protected $title = 'Rates export';

    public function __construct($ids = [],$title = null){
        $this->ids = $ids;
        if($title){
            $this->title=$title;
        }
    }

 
     public function collection()
    {
        $model= \App\Models\Rate::with(['fabric_type','fabric_color','size']);


        if(count($this->ids) > 0){
            $model->whereIn('id',$this->ids);
        }
        $items = $model->get();
        
        return $items;
    }

    
     public function headings(): array{     
        return [
           'Fabric Type', 'Fabric Color','Size','Rate','Date','Last Updated'];
    }

    public function map($item): array{
        return [
            $item->fabric_type_name,
            $item->fabric_color_name,
            $item->size_height_width,
            $item->rate,
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
