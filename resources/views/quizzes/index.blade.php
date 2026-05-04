@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">All Quizzes</h2>
    <a href="{{ route('quizzes.create') }}" class="btn btn-primary">+ Create Quiz</a>
</div>

@if($quizzes->isEmpty())
    <div class="card p-5 text-center text-muted">
        <h5>No quizzes yet</h5>
        <p>Click "Create Quiz" to get started.</p>
    </div>
@else
    <div class="row g-4">
        @foreach($quizzes as $quiz)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 p-4">
                <h5 class="fw-bold">{{ $quiz->title }}</h5>
                <p class="text-muted small">{{ $quiz->description ?? 'No description.' }}</p>
                <p class="text-muted small mb-3">{{ $quiz->questions_count }} question(s)</p>
                <div class="mt-auto d-flex gap-2 flex-wrap">
                    <a href="{{ route('attempts.show', $quiz) }}" class="btn btn-success btn-sm">▶ Attempt</a>
                    <a href="{{ route('quizzes.edit', $quiz) }}" class="btn btn-outline-secondary btn-sm">✏ Edit</a>
                    <form action="{{ route('quizzes.destroy', $quiz) }}" method="POST"
                          onsubmit="return confirm('Delete this quiz?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm">🗑 Delete</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection 