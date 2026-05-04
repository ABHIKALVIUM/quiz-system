<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\AttemptController;

Route::get('/', function () {
    return redirect()->route('quizzes.index');
});

// Quiz management routes
Route::get('/quizzes', [QuizController::class, 'index'])->name('quizzes.index');
Route::get('/quizzes/create', [QuizController::class, 'create'])->name('quizzes.create');
Route::post('/quizzes', [QuizController::class, 'store'])->name('quizzes.store');
Route::get('/quizzes/{quiz}/edit', [QuizController::class, 'edit'])->name('quizzes.edit');
Route::put('/quizzes/{quiz}', [QuizController::class, 'update'])->name('quizzes.update');
Route::delete('/quizzes/{quiz}', [QuizController::class, 'destroy'])->name('quizzes.destroy');

// Question routes
Route::post('/quizzes/{quiz}/questions', [QuizController::class, 'storeQuestion'])->name('quizzes.questions.store');
Route::delete('/quizzes/{quiz}/questions/{question}', [QuizController::class, 'destroyQuestion'])->name('quizzes.questions.destroy');

// Quiz attempt routes
Route::get('/quizzes/{quiz}/attempt', [AttemptController::class, 'show'])->name('attempts.show');
Route::post('/quizzes/{quiz}/attempt', [AttemptController::class, 'store'])->name('attempts.store');
Route::get('/attempts/{attempt}/result', [AttemptController::class, 'result'])->name('attempts.result');