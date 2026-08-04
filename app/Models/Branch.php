<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address', 'phone', 'is_active'];

    public function barbers()
    {
        return $this->hasMany(Barber::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
