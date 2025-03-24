<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RateChart extends Model {
    
    use HasFactory;

    protected $table = 'rate_charts';

    protected $fillable = [
        'user_id',
        'weight',
        'rate_amount',
    ];
    
    protected $casts = [
        'weight' => 'decimal:2',
        'rate_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
