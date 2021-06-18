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


class UsersExport implements FromCollection,WithStyles,WithMapping,WithHeadings,WithProperties,ShouldAutoSize,WithTitle,WithCustomCsvSettings
{

    protected $data = [];
    protected $for = 'Users';
    protected $title = 'Users export';

    public function __construct($data = [],$title = null){
        $this->data = $data;
        if($title){
            $this->title=$title;
        }
    }

 
     public function collection()
    {

        $role_name = 0;

        if(array_key_exists('role',$this->data)){
            $role_name = $this->data['role'];
        }
        
        $roles = \App\Models\Role::all();
        $auth_user= auth()->user();
        $permitable_roles= array();
        
        foreach($roles as $role){
            if($auth_user->can('auth-manage-'.str_replace(' ','-',strtolower($role->name)))){
                $permitable_roles[]=$role->id;
            }
        }

        
        $model= \App\Models\User::whereHas('roles', function ($query) use ($permitable_roles,$role_name) {
                $query->when($role_name,function ($query, $role_name) {
                    return $query->where('name', $role_name);
                })->whereIn('id',$permitable_roles);
            return $query;
        });
        
        $items = $model->get();

        return $items;
    }

    
     public function headings(): array{     
        return [
           'First Name', 'Last Name','Email','Role','Registered Date','Last Updated Date',
        ];
    }

    public function map($item): array{
        return [
            $item->first_name,
            $item->last_name,
            $item->email,
            implode(',',$item->roles->pluck('name')->toArray()),
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
