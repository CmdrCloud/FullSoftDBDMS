<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $table = 'vehicle';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'model',
        'brand',
        'cylinders',
        'numberPlate',
        'year',
        'imgPath',
        'airConditioning',
        'metallicPaint',
        'partOfPayment',
        'price'
    ];
    protected $casts = [
        'airConditioning' => 'boolean',
        'metallicPaint' => 'boolean',
        'partOfPayment' => 'boolean',
        'price' => 'decimal:2'
    ];
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

}
