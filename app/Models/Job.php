<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    public function jobType()
    {
        return $this->belongsTo(jobType::class);
    }
    public function category()
    {
        return $this->belongsTo(category::class);
    }
    public function application()
    {
        return $this->hasMany(JobApplication::class);
    }

    public function user()
    {
        return $this->belongsTo(user::class);
    }
}
