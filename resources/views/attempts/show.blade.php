@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card p-4 mb-4">
            <h2 class="fw-bold">{{ $quiz->title }}</h2>
            @if($quiz->description)
                <p class="text-muted">{{ $quiz->description }}</p>
            @endif
            <p class="mb-0 small text-muted">{{ $quiz->questions->count() }} question(s) &bull; Total marks: {{ $quiz->totalMarks() }}</p>
        </div>

        @if($quiz->questions->isEmpty())
            <div class="card p-4 text-center text-muted">
                <p>This quiz has no questions yet.</p>
                <a href="{{ route('quizzes.edit', $quiz) }}" class="btn btn-primary btn-sm">Add Questions</a>
            </div>
        @else
        <form action="{{ route('attempts.store', $quiz) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="card p-4 mb-4">
                <label class="form-label fw-semibold">Your Name</label>
                <input type="text" name="participant_name" class="form-control"
                       placeholder="Enter your name" required>
            </div>

            @foreach($quiz->questions as $i => $question)
            <div class="card p-4 mb-3">
                <div class="d-flex align-items-start gap-2 mb-2">
                    <span class="badge bg-secondary">Q{{ $i + 1 }}</span>
                    <span class="badge bg-info">{{ $question->marks }} mark(s)</span>
                    <span class="badge bg-light text-dark border">{{ strtoupper($question->type) }}</span>
                </div>

                <p class="fw-semibold mb-2">{!! $question->body !!}</p>

                @if($question->image)
                    <img src="{{ Storage::url($question->image) }}"
                         class="img-fluid rounded mb-3" style="max-height: 250px; max-width: 100%; width: auto; object-fit: contain;">
                @endif

                @if($question->video_url)
                    <div class="mb-3">
                        @php
                            preg_match('/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $question->video_url, $matches);
                            $videoId = $matches[1] ?? null;
                        @endphp
                        @if($videoId)
                            <div class="ratio ratio-16x9" style="max-width:400px;">
                                <iframe src="https://www.youtube.com/embed/{{ $videoId }}"
                                        allowfullscreen></iframe>
                            </div>
                        @else
                            <a href="{{ $question->video_url }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                🎬 Watch Video
                            </a>
                        @endif
                    </div>
                @endif

                {{-- Binary --}}
                @if($question->type === 'binary')
                    <div class="d-flex gap-4 mt-2">
                        @foreach($question->options as $option)
                        <div class="form-check">
                            <input class="form-check-input" type="radio"
                                   name="answers[{{ $question->id }}]"
                                   value="{{ $option->label }}"
                                   id="opt_{{ $option->id }}" required>
                            <label class="form-check-label fw-semibold" for="opt_{{ $option->id }}">
                                {{ $option->label }}
                            </label>
                        </div>
                        @endforeach
                    </div>

                {{-- Single Choice --}}
                @elseif($question->type === 'single')
                    <div class="mt-2">
                        @foreach($question->options as $option)
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio"
                                   name="answers[{{ $question->id }}]"
                                   value="{{ $option->id }}"
                                   id="opt_{{ $option->id }}" required>
                            <label class="form-check-label d-flex align-items-center gap-2" for="opt_{{ $option->id }}">
                                @if($option->image)
                                    <img src="{{ Storage::url($option->image) }}"
                                         style="max-height:50px;" class="rounded">
                                @endif
                                {{ $option->label }}
                            </label>
                        </div>
                        @endforeach
                    </div>

                {{-- Multiple Choice --}}
                @elseif($question->type === 'multiple')
                    <div class="mt-2">
                        <p class="text-muted small mb-2">Select all that apply.</p>
                        @foreach($question->options as $option)
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox"
                                   name="answers[{{ $question->id }}][]"
                                   value="{{ $option->id }}"
                                   id="opt_{{ $option->id }}">
                            <label class="form-check-label d-flex align-items-center gap-2" for="opt_{{ $option->id }}">
                                @if($option->image)
                                    <img src="{{ Storage::url($option->image) }}"
                                         style="max-height:50px;" class="rounded">
                                @endif
                                {{ $option->label }}
                            </label>
                        </div>
                        @endforeach
                    </div>

                {{-- Number Input --}}
                @elseif($question->type === 'number')
                    <input type="number" step="any"
                           name="answers[{{ $question->id }}]"
                           class="form-control mt-2" style="max-width:200px;"
                           placeholder="Enter a number" required>

                {{-- Text Input --}}
                @elseif($question->type === 'text')
                    <input type="text"
                           name="answers[{{ $question->id }}]"
                           class="form-control mt-2"
                           placeholder="Type your answer here" required>
                @endif
            </div>
            @endforeach

            <div class="text-end mt-3">
                <button type="submit" class="btn btn-success px-5"
                        onclick="return confirm('Submit your answers?')">
                    Submit Quiz
                </button>
            </div>
        </form>
        @endif
    </div>
</div>
@endsection