<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class salary extends Model
{
    use HasFactory;

    protected $fillable=[
        'usr_id',
        'range'
    ];


    public static function getEnumValues()
    {
        $table = 'salaries';
        $column = 'range';
        
        $enumValues = DB::select("SHOW COLUMNS FROM $table WHERE Field = '$column'")[0]->Type;
        preg_match("/^enum\((.*)\)$/", $enumValues, $matches);
        $enumOptions = explode(',', $matches[1]);
        $enumValuesArray = [];
        foreach ($enumOptions as $option) {
            $option = trim($option, "'");
            $enumValuesArray[] = $option;
        }

        return $enumValuesArray;
    }
}
