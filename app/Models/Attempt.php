<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attempt extends Model
{
    protected $fillable = [
        'quiz_id', 'participant_name', 'score', 'total_marks', 'submitted_at'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function percentage()
    {
        if ($this->total_marks === 0) return 0;
        return round(($this->score / $this->total_marks) * 100, 1);
    }
}