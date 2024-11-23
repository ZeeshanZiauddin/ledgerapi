<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Company extends Model
{
    use HasFactory;

    // Allow mass assignment for the specified fields
    protected $fillable = [
        'title',
        'slug',
        'description',
        'seo_keywords',
        'api_key',
        'created_by',
        'email',
        'phone',
        'logo',
        'favicon',
        'location',
        'status', // New fields added
    ];

    // Generate a unique API key for the company
    public static function generateApiKey()
    {
        return Str::uuid()->toString();
    }

    // Define the relationship with the Query model
    public function queries()
    {
        return $this->hasMany(Query::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }


    // Set default values for fields
    protected $attributes = [
        'status' => true, // Default status is active
    ];
}
