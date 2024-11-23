<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'message',
        'company_id', // Ensure company_id is fillable
    ];
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

}

