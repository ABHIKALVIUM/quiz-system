<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'quiz_id', 'body', 'type', 'image', 'video_url', 'marks', 'order'
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function options()
    {
        return $this->hasMany(Option::class)->orderBy('order');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    // This is the key method - each question type knows how to evaluate itself
    public function evaluate($value): bool
    {
        return match($this->type) {
            'binary'   => $this->evaluateBinary($value),
            'single'   => $this->evaluateSingle($value),
            'multiple' => $this->evaluateMultiple($value),
            'number'   => $this->evaluateNumber($value),
            'text'     => $this->evaluateText($value),
            default    => false,
        };
    }

    private function evaluateBinary($value): bool
    {
        $correct = $this->options->where('is_correct', true)->first();
        return $correct && strtolower($correct->label) === strtolower($value);
    }

    private function evaluateSingle($value): bool
    {
        $correct = $this->options->where('is_correct', true)->first();
        return $correct && (string)$correct->id === (string)$value;
    }

    private function evaluateMultiple($value): bool
    {
        $submitted = is_array($value) ? $value : json_decode($value, true) ?? [];
        $correctIds = $this->options->where('is_correct', true)->pluck('id')
                          ->map(fn($id) => (string)$id)->sort()->values()->toArray();
        $submittedIds = collect($submitted)->map(fn($id) => (string)$id)
                            ->sort()->values()->toArray();
        return $correctIds === $submittedIds;
    }

    private function evaluateNumber($value): bool
    {
        $correct = $this->options->first();
        return $correct && (float)$correct->label === (float)$value;
    }

    private function evaluateText($value): bool
    {
        $correct = $this->options->first();
        return $correct && strtolower(trim($correct->label)) === strtolower(trim($value));
    }
}