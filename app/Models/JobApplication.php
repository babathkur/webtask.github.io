<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    use HasFactory;
    public function job()
    {
        return $this->belongsTo(job::class);
    }
    public function user()
    {
        return $this->belongsTo(user::class);
    }

    public function employer()
    {
        return $this->belongsTo(user::class, 'employer_id');
    }
}
