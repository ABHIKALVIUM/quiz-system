@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card p-4">
            <h3 class="fw-bold mb-4">Create New Quiz</h3>
            <form action="{{ route('quizzes.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Quiz Title</label>
                    <input type="text" name="title" class="form-control"
                           value="{{ old('title') }}" placeholder="e.g. General Knowledge" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Description <span class="text-muted fw-normal">(optional)</span></label>
                    <textarea name="description" class="form-control" rows="3"
                              placeholder="What is this quiz about?">{{ old('description') }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary px-4">Create & Add Questions →</button>
                <a href="{{ route('quizzes.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection