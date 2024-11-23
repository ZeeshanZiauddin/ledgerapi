<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Query extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'queries';

    // Specify the fields that are mass assignable
    protected $fillable = [
        'departure',
        'arrival',
        'first_name',
        'last_name',
        'email',
        'passengers',
        'date_range',
        'company_id',
    ];

    // Define the relationship with the Company model
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
