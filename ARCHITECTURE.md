# Architecture & Design Decisions

## Overview

The system follows standard Laravel MVC architecture with a strong focus on
extensibility. The core design goal was to avoid hardcoding evaluation logic
for each question type in multiple places.

## Database Design

### quizzes
Stores quiz title and description. Simple and clean.

### questions
Each question belongs to a quiz and has a `type` field (binary, single,
multiple, number, text). Media fields (image, video_url) are optional.
The `marks` field defaults to 1 but can be set per question.

### options
Stores answer choices for a question. Works for all types:
- binary/single/multiple: multiple rows, is_correct flags the right answer
- number/text: single row with the correct value in label field
- Options support both text label and image for rich options

### attempts
Records each quiz attempt with participant name, score, and total marks.
Submitted_at timestamp records when the quiz was completed.

### answers
Stores each answer given during an attempt. The `value` column stores
the raw answer (JSON for multiple choice, plain string for others).
is_correct and marks_awarded are calculated at submission time.

## Extensibility — The Key Design Decision

The most important design decision is the `evaluate()` method on the
Question model. Instead of putting if/else chains in the controller,
each question delegates evaluation to itself:

```php
public function evaluate($value): bool {
    return match($this->type) {
        'binary'   => $this->evaluateBinary($value),
        'single'   => $this->evaluateSingle($value),
        'multiple' => $this->evaluateMultiple($value),
        'number'   => $this->evaluateNumber($value),
        'text'     => $this->evaluateText($value),
        default    => false,
    };
}
```

To add a new question type (e.g. "ordering"), you only need to:
1. Add the type to the match statement
2. Write one private evaluateOrdering() method
3. Add the UI for it in the edit and attempt views

No other files need to change. The controller stays clean.

## Controllers

### QuizController
Handles all quiz and question CRUD. The saveOptions() method centralises
all option-saving logic so it is not repeated across multiple actions.

### AttemptController
Handles quiz attempts. The store() method loops through questions,
calls question->evaluate(), and calculates scores. It does not know
anything about specific question types — that knowledge lives in the model.

## Storage

Media files (question images, option images) are stored in
storage/app/public and served via the public storage symlink.
This keeps the database clean and files organised.

## Frontend

Plain Blade templates with Bootstrap 5. JavaScript is used only for the
dynamic option builder in the question editor — no frameworks needed.