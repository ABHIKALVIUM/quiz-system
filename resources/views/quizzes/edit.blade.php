@extends('layouts.app')

@section('content')
<div class="row justify-content-between align-items-center mb-4">
    <div class="col">
        <h2 class="fw-bold mb-0">Edit Quiz</h2>
    </div>
    <div class="col-auto">
        <a href="{{ route('attempts.show', $quiz) }}" class="btn btn-success btn-sm">▶ Preview & Attempt</a>
    </div>
</div>

{{-- Quiz title/description edit --}}
<div class="card p-4 mb-4">
    <h5 class="fw-semibold mb-3">Quiz Details</h5>
    <form action="{{ route('quizzes.update', $quiz) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3">
            <label class="form-label fw-semibold">Title</label>
            <input type="text" name="title" class="form-control" value="{{ $quiz->title }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Description</label>
            <textarea name="description" class="form-control" rows="2">{{ $quiz->description }}</textarea>
        </div>
        <button class="btn btn-primary btn-sm">Save Changes</button>
    </form>
</div>

{{-- Existing questions --}}
<div class="card p-4 mb-4">
    <h5 class="fw-semibold mb-3">Questions ({{ $quiz->questions->count() }})</h5>
    @if($quiz->questions->isEmpty())
        <p class="text-muted">No questions yet. Add one below.</p>
    @else
        @foreach($quiz->questions as $i => $question)
        <div class="border rounded p-3 mb-3 bg-light">
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <span class="badge bg-secondary badge-type me-2">{{ strtoupper($question->type) }}</span>
                    <span class="badge bg-info badge-type me-2">{{ $question->marks }} mark(s)</span>
                    <p class="mt-2 mb-1 fw-semibold">{!! $question->body !!}</p>
                    @if($question->image)
                        <img src="{{ Storage::url($question->image) }}" class="img-thumbnail mt-1" style="max-height:80px;">
                    @endif
                    @if($question->video_url)
                        <p class="small text-muted mb-1">🎬 {{ $question->video_url }}</p>
                    @endif
                    @if($question->options->isNotEmpty())
                        <ul class="mt-2 mb-0 small">
                            @foreach($question->options as $opt)
                                <li class="{{ $opt->is_correct ? 'text-success fw-bold' : '' }}">
                                    {{ $opt->label ?? '' }}
                                    @if($opt->is_correct) ✓ @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <form action="{{ route('quizzes.questions.destroy', [$quiz, $question]) }}"
                      method="POST" onsubmit="return confirm('Remove this question?')" class="ms-3">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm">✕</button>
                </form>
            </div>
        </div>
        @endforeach
    @endif
</div>

{{-- Add new question --}}
<div class="card p-4">
    <h5 class="fw-semibold mb-3">Add New Question</h5>
    <form action="{{ route('quizzes.questions.store', $quiz) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-semibold">Question Text (HTML allowed)</label>
            <textarea name="body" id="questionBody" class="form-control" rows="3"
                      placeholder="Type your question here..." required></textarea>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Question Type</label>
                <select name="type" id="questionType" class="form-select" required>
                    <option value="">-- Select Type --</option>
                    <option value="binary">Binary (Yes/No)</option>
                    <option value="single">Single Choice</option>
                    <option value="multiple">Multiple Choice</option>
                    <option value="number">Number Input</option>
                    <option value="text">Text Input</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Marks</label>
                <input type="number" name="marks" class="form-control" value="1" min="1">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Question Image <span class="text-muted fw-normal">(optional)</span></label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Video URL <span class="text-muted fw-normal">(YouTube link, optional)</span></label>
            <input type="url" name="video_url" class="form-control" placeholder="https://youtube.com/watch?v=...">
        </div>

        {{-- Binary options --}}
        <div id="binaryOptions" class="option-block d-none mb-3">
            <label class="form-label fw-semibold">Correct Answer</label>
            <div class="d-flex gap-3">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="correct_binary" value="Yes" id="binYes" checked>
                    <label class="form-check-label" for="binYes">Yes</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="correct_binary" value="No" id="binNo">
                    <label class="form-check-label" for="binNo">No</label>
                </div>
            </div>
        </div>

        {{-- Single / Multiple choice options --}}
        <div id="choiceOptions" class="option-block d-none mb-3">
            <label class="form-label fw-semibold">Options</label>
            <div id="optionsList"></div>
            <button type="button" class="btn btn-outline-secondary btn-sm mt-2" onclick="addOption()">+ Add Option</button>
            <p class="text-muted small mt-1" id="choiceHint"></p>
        </div>

        {{-- Number / Text answer --}}
        <div id="directAnswer" class="option-block d-none mb-3">
            <label class="form-label fw-semibold" id="directLabel">Correct Answer</label>
            <input type="text" name="correct_answer" class="form-control" placeholder="Enter the correct answer">
        </div>

        <button type="submit" class="btn btn-primary">Add Question</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    let optionIndex = 0;
    let currentType = '';

    document.getElementById('questionType').addEventListener('change', function () {
        currentType = this.value;
        document.querySelectorAll('.option-block').forEach(el => el.classList.add('d-none'));
        optionIndex = 0;
        document.getElementById('optionsList').innerHTML = '';

        if (currentType === 'binary') {
            document.getElementById('binaryOptions').classList.remove('d-none');
        } else if (currentType === 'single' || currentType === 'multiple') {
            document.getElementById('choiceOptions').classList.remove('d-none');
            document.getElementById('choiceHint').textContent =
                currentType === 'multiple' ? 'Check all correct options.' : 'Select one correct option.';
            addOption();
            addOption();
        } else if (currentType === 'number' || currentType === 'text') {
            document.getElementById('directAnswer').classList.remove('d-none');
            document.getElementById('directLabel').textContent =
                currentType === 'number' ? 'Correct Number' : 'Correct Text Answer';
        }
    });

    function addOption() {
        const list = document.getElementById('optionsList');
        const inputType = currentType === 'multiple' ? 'checkbox' : 'radio';
        const div = document.createElement('div');
        div.className = 'border rounded p-2 mb-2 bg-light';
        div.innerHTML = `
            <div class="row g-2 align-items-center">
                <div class="col-auto">
                    <input type="${inputType}" name="correct_options[]" value="${optionIndex}"
                           class="form-check-input mt-0" title="Mark as correct">
                </div>
                <div class="col">
                    <input type="text" name="option_label[]" class="form-control form-control-sm"
                           placeholder="Option text (optional if image used)">
                </div>
                <div class="col">
                    <input type="file" name="option_image[]" class="form-control form-control-sm" accept="image/*">
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-outline-danger btn-sm"
                            onclick="this.closest('div.border').remove()">✕</button>
                </div>
            </div>`;
        list.appendChild(div);
        optionIndex++;
    }
</script>
@endsection