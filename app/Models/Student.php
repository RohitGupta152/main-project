<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Student extends Model
{
    use HasFactory;

    protected $table = 'students';

    public $timestamps = false;


    protected $fillable = [
        'name',
        'email',
        'age',
        'course',
        'created_date',
        'updated_date'
    ];

    /* //Automatically format timestamps in JSON response
    protected $casts = [
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
    ]; */
}
