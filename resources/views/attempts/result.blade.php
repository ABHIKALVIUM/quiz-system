@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">

        {{-- Score card --}}
        <div class="card p-4 mb-4 text-center">
            @php
                $pct = $attempt->percentage();
                $color = $pct >= 70 ? 'success' : ($pct >= 40 ? 'warning' : 'danger');
            @endphp
            <h2 class="fw-bold mb-1">{{ $attempt->participant_name }}'s Result</h2>
            <p class="text-muted mb-3">{{ $attempt->quiz->title }}</p>
            <div class="display-4 fw-bold text-{{ $color }} mb-1">
                {{ $attempt->score }} / {{ $attempt->total_marks }}
            </div>
            <p class="text-muted">{{ $pct }}% &bull; Submitted {{ $attempt->submitted_at->format('d M Y, h:i A') }}</p>
            <div class="progress mt-2" style="height:12px;">
                <div class="progress-bar bg-{{ $color }}" style="width: {{ $pct }}%"></div>
            </div>
            <p class="mt-3 fw-semibold fs-5">
                @if($pct >= 70) 🎉 Great job!
                @elseif($pct >= 40) 👍 Good effort!
                @else 📚 Keep practising!
                @endif
            </p>
        </div>

        {{-- Answer breakdown --}}
        <div class="card p-4 mb-4">
            <h5 class="fw-bold mb-3">Answer Breakdown</h5>
            @foreach($attempt->answers as $i => $answer)
            @php $question = $answer->question; @endphp
            <div class="border rounded p-3 mb-3 {{ $answer->is_correct ? 'border-success' : 'border-danger' }}">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="fw-semibold">Q{{ $i + 1 }}: {!! $question->body !!}</span>
                    <span class="badge bg-{{ $answer->is_correct ? 'success' : 'danger' }} ms-2">
                        {{ $answer->marks_awarded }} / {{ $question->marks }}
                    </span>
                </div>

                {{-- Show submitted answer --}}
                <p class="mb-1 small">
                    <strong>Your answer:</strong>
                    @if($question->type === 'multiple')
                        @php $ids = json_decode($answer->value, true) ?? []; @endphp
                        @if(count($ids))
                            {{ $question->options->whereIn('id', $ids)->pluck('label')->join(', ') }}
                        @else
                            <em class="text-muted">No answer given</em>
                        @endif
                    @elseif($question->type === 'single')
                        {{ $question->options->firstWhere('id', $answer->value)?->label ?? $answer->value }}
                    @else
                        {{ $answer->value ?: '—' }}
                    @endif
                </p>

                {{-- Show correct answer --}}
                @if(!$answer->is_correct)
                <p class="mb-0 small text-success">
                    <strong>Correct answer:</strong>
                    @if(in_array($question->type, ['binary', 'text', 'number']))
                        {{ $question->options->where('is_correct', true)->first()?->label }}
                    @elseif($question->type === 'single')
                        {{ $question->options->where('is_correct', true)->first()?->label }}
                    @elseif($question->type === 'multiple')
                        {{ $question->options->where('is_correct', true)->pluck('label')->join(', ') }}
                    @endif
                </p>
                @endif
            </div>
            @endforeach
        </div>

        <div class="d-flex gap-3 justify-content-center">
            <a href="{{ route('attempts.show', $attempt->quiz) }}" class="btn btn-primary">Try Again</a>
            <a href="{{ route('quizzes.index') }}" class="btn btn-outline-secondary">All Quizzes</a>
        </div>
    </div>
</div>
@endsection