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


class CutPiecesExport implements FromCollection,WithStyles,WithMapping,WithHeadings,WithProperties,ShouldAutoSize,WithTitle,WithCustomCsvSettings
{

    protected $data = [];
    protected $for = 'Cut Pieces';
    protected $title = 'Cut Pieces export';

    public function __construct($data = [],$title = null){
        $this->data = $data;
        if($title){
            $this->title=$title;
        }
    }

 
     public function collection()
    {
        $fabric_color_id = false;
        $fabric_type_id = false;
        $size_id = false;

        if(array_key_exists('fabric_type',$this->data)){
            $fabric_type_id = $this->data['fabric_type'];
        }
        
        if(array_key_exists('fabric_color',$this->data)){
            $fabric_color_id = $this->data['fabric_color'];
        }

        if(array_key_exists('size',$this->data)){
            $size_id = $this->data['size'];
        }
        
        $model= \App\Models\CutPiece::when($fabric_type_id,function ($query, $fabric_type_id) {
                    return $query->where('fabric_type_id', $fabric_type_id);
                })->when($fabric_color_id,function($query,$fabric_color_id){
                    return $query->where('fabric_color_id',$fabric_color_id);
                })->when($size_id,function($query,$size_id){
                    return $query->where('size_id',$size_id);
                })->with(['fabric_type:id,name','fabric_color:id,name','size:id,width,height']);


         
 
        $model->withSum('used_pieces','used_pieces');

        $items = $model->get();
        
        return $items;
    }

    
     public function headings(): array{     
        return [
           'Fabric Type', 'Fabric Color','Size','Total Pieces','Used Pieces','Remaining Pieces','Created Date','Last Updated Date'
        ];
    }

    public function map($item): array{
        return [
            $item->fabric_type_name,
            $item->fabric_color_name,
            $item->size_height_width,
            $item->pieces,
            $item->used_pieces_sum_used_pieces ?: '0',
            ($item->pieces-$item->used_pieces_sum_used_pieces) ?: '0' ,
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
