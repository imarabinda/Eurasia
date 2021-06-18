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


class FinalStocksExport implements FromCollection,WithStyles,WithMapping,WithHeadings,WithProperties,ShouldAutoSize,WithTitle,WithCustomCsvSettings
{

    protected $ids = [];
    protected $for = 'Final Stock';
    protected $title = 'Final Stock export';

    public function __construct($ids = [],$title = null){
        $this->ids = $ids;
        if($title){
            $this->title=$title;
        }
    }

 
     public function collection()
    {
        $model= \App\Models\FinalStock::with(['product:id,name,code,fabric_type_id,fabric_color_id,size_id,product_type_id,product_category_id','product.fabric_type:id,name','product.fabric_color:id,name','product.size:id,height,width','product.product_category:id,name','product.product_type:id,name','shipments']);

        if(count($this->ids) > 0){
            $model->whereIn('id',$this->ids);
        }
        $items = $model->get();
        
        return $items;
    }

    
     public function headings(): array{     
        return [
           'Receive Date', 'Name','Category','Type','Fabric Type','Fabric Color','Size','Stock Quantity','Used Stock','Remaining Stock','Damaged Stock Quantity'
        ];
    }

    public function map($item): array{
        return [
            $item->updated_at,
            $item->product->name,
            $item->product->product_category_name,
            $item->product->product_type_name,
            $item->product->fabric_type_name,
            $item->product->fabric_color_name,
            $item->product->size_height_width,
            $item->received_stitches ?: '0',
            $item->shipments->sum("pivot.issued_quantity") ?: '0',
            ($item->received_stitches-$item->shipments->sum('pivot.issued_quantity')) ?: '0',
            $item->received_damage ?: '0',
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
