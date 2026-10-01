<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'document',
        'first_name',
        'last_name',
        'birth_date',
        'sex',
        'phone',
        'email',
        'address',
        'emergency_contact',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function appointments()
{
    return $this->hasMany(Appointment::class);
}

public function clinicalHistory()
{
    return $this->hasOne(ClinicalHistory::class);
}
}