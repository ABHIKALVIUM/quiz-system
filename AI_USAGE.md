# AI Usage Documentation

## Overview

AI assistance (Claude by Anthropic) was used throughout this project
for guidance, code generation, and debugging. This document details
how AI was used and where human judgment was applied.

## How AI Was Used

### 1. Project Setup & Environment
- Used AI to get step-by-step instructions for installing PHP, Composer
  on Windows 10
- AI helped diagnose and resolve SQLite driver issues when MySQL was
  unavailable
- Prompts like: "I am new to Laravel, guide me step by step to set up
  the project on Windows"

### 2. Database Design
- Asked AI to suggest a database schema for a flexible quiz system
- Prompt: "Design a MySQL schema for a quiz system that supports binary,
  single choice, multiple choice, number and text question types and
  is extensible for future types"
- Reviewed and adjusted the schema — added the `order` column and
  ensured the answers table stored raw values for auditability

### 3. Model & Evaluation Logic
- Key prompt: "How can I design the evaluation logic so it is not
  hardcoded for each question type in multiple places?"
- AI suggested the evaluate() method pattern on the Question model
  using PHP match expressions
- Reviewed the logic manually and tested edge cases like empty multiple
  choice submissions and case-insensitive text comparison

### 4. Controllers
- Prompted AI to generate QuizController and AttemptController
- Reviewed the saveOptions() method and corrected index handling for
  multiple choice correct options
- Verified that the score calculation loop was correct

### 5. Blade Views
- Prompted AI to generate Bootstrap 5 Blade templates
- Manually adjusted styling, spacing and labels to make the UI cleaner
- Added the YouTube embed parsing logic after reviewing the output

## Corrections Made to AI Output

1. The initial multiple choice evaluation had a bug where option indexes
   did not match option IDs — fixed by using option IDs consistently
2. The result page initially did not show correct answers for wrong
   questions — added that section manually
3. Storage symlink step was missing from initial setup — added after
   images were not loading

## Conclusion

AI was used as a knowledgeable assistant to speed up boilerplate and
suggest architectural patterns. All code was reviewed, tested, and
adjusted manually. The core design decision — the evaluate() method
pattern — was understood and validated before implementation.