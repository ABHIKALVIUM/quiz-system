<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::withCount('questions')->latest()->get();
        return view('quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        return view('quizzes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $quiz = Quiz::create($request->only('title', 'description'));

        return redirect()->route('quizzes.edit', $quiz)
                         ->with('success', 'Quiz created! Now add your questions.');
    }

    public function edit(Quiz $quiz)
    {
        $quiz->load('questions.options');
        return view('quizzes.edit', compact('quiz'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $quiz->update($request->only('title', 'description'));

        return redirect()->route('quizzes.edit', $quiz)
                         ->with('success', 'Quiz updated successfully.');
    }

    public function destroy(Quiz $quiz)
    {
        $quiz->delete();
        return redirect()->route('quizzes.index')
                         ->with('success', 'Quiz deleted.');
    }

    public function storeQuestion(Request $request, Quiz $quiz)
    {
        $request->validate([
            'body'      => 'required|string',
            'type'      => 'required|in:binary,single,multiple,number,text',
            'marks'     => 'required|integer|min:1',
            'image'     => 'nullable|image|max:2048',
            'video_url' => 'nullable|url',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('questions', 'public');
        }

        $order = $quiz->questions()->count();

        $question = $quiz->questions()->create([
            'body'      => $request->body,
            'type'      => $request->type,
            'marks'     => $request->marks,
            'image'     => $imagePath,
            'video_url' => $request->video_url,
            'order'     => $order,
        ]);

        $this->saveOptions($request, $question);

        return redirect()->route('quizzes.edit', $quiz)
                         ->with('success', 'Question added.');
    }

    public function destroyQuestion(Quiz $quiz, Question $question)
    {
        $question->delete();
        return redirect()->route('quizzes.edit', $quiz)
                         ->with('success', 'Question removed.');
    }

    private function saveOptions(Request $request, Question $question)
    {
        $question->options()->delete();

        $type = $question->type;

        if ($type === 'binary') {
            $options  = ['Yes', 'No'];
            $correct  = $request->input('correct_binary', 'Yes');
            foreach ($options as $i => $label) {
                $question->options()->create([
                    'label'      => $label,
                    'is_correct' => $label === $correct,
                    'order'      => $i,
                ]);
            }
            return;
        }

        if (in_array($type, ['single', 'multiple'])) {
            $labels   = $request->input('option_label', []);
            $images   = $request->file('option_image', []);
            $corrects = $request->input('correct_options', []);

            foreach ($labels as $i => $label) {
                $imgPath = null;
                if (!empty($images[$i])) {
                    $imgPath = $images[$i]->store('options', 'public');
                }
                $question->options()->create([
                    'label'      => $label,
                    'image'      => $imgPath,
                    'is_correct' => in_array((string)$i, array_map('strval', $corrects)),
                    'order'      => $i,
                ]);
            }
            return;
        }

        if (in_array($type, ['number', 'text'])) {
            $question->options()->create([
                'label'      => $request->input('correct_answer'),
                'is_correct' => true,
                'order'      => 0,
            ]);
        }
    }
}