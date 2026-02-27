<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    use HasFactory;
    protected $table = 'landing_page';
    protected $primaryKey = 'id';
    protected $guarded = [];
    public $timestamps = false;
    public $incrementing = true; 
    protected $appends = array('pathfile');

    public function getPathfileAttribute()
    {
        $filePath=url('storage').'/'.$this->file_path."/".$this->photo_path;
        return $filePath;
    }
}
