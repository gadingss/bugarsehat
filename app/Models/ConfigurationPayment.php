<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigurationPayment extends Model
{
    protected $fillable = ['metode_pembayaran', 'rekening', 'atas_nama'];
    use HasFactory;
}
