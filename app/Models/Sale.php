<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $table = 'sale';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'IDVehicle',
        'IDClient',
        'IDUser',
        'date',
        'totalAmount',
        'totalUpfront',
        'totalPartPayment',
    ];

    protected $casts = [
        'totalAmount' => 'float',
        'totalUpfront' => 'float',
        'totalPartPayment' => 'float',
        'date' => 'datetime'
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'IDVehicle');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'IDClient');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'IDUser');
    }
}
