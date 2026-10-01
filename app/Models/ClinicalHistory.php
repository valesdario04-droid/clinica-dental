<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicalHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'created_by',
        'medical_background',
        'allergies',
        'general_notes',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function consultations()
    {
        return $this->hasMany(ClinicalConsultation::class);
    }
}