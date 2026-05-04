<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Attempt;
use App\Models\Answer;
use Illuminate\Http\Request;

class AttemptController extends Controller
{
    public function show(Quiz $quiz)
    {
        $quiz->load('questions.options');
        return view('attempts.show', compact('quiz'));
    }

    public function store(Request $request, Quiz $quiz)
    {
        $request->validate([
            'participant_name' => 'required|string|max:255',
        ]);

        $quiz->load('questions.options');

        $attempt = Attempt::create([
            'quiz_id'          => $quiz->id,
            'participant_name' => $request->participant_name,
            'score'            => 0,
            'total_marks'      => $quiz->totalMarks(),
            'submitted_at'     => now(),
        ]);

        $totalScore = 0;

        foreach ($quiz->questions as $question) {
            $raw = $request->input('answers.' . $question->id);

            // normalize multiple choice to array
            if ($question->type === 'multiple') {
                $raw = $raw ?? [];
            }

            $isCorrect    = $question->evaluate($raw);
            $marksAwarded = $isCorrect ? $question->marks : 0;
            $totalScore  += $marksAwarded;

            Answer::create([
                'attempt_id'   => $attempt->id,
                'question_id'  => $question->id,
                'value'        => is_array($raw) ? json_encode($raw) : (string)($raw ?? ''),
                'is_correct'   => $isCorrect,
                'marks_awarded'=> $marksAwarded,
            ]);
        }

        $attempt->update(['score' => $totalScore]);

        return redirect()->route('attempts.result', $attempt)
                         ->with('success', 'Quiz submitted!');
    }

    public function result(Attempt $attempt)
    {
        $attempt->load('quiz', 'answers.question.options');
        return view('attempts.result', compact('attempt'));
    }
}