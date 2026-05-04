# AI Usage Documentation

## Overview

This project was built independently with a basic understanding of Laravel.
AI assistance was taken in limited areas — primarily for initial boilerplate
and database schema suggestions. All core logic, evaluation design, controller
structure, and debugging was done manually.

## Areas Built Without AI

### 1. Project Setup & Environment
Set up PHP, Composer and Laravel on Windows manually by reading official
documentation. Resolved SQLite driver issues by editing php.ini directly
and enabling the required extensions. The .env configuration was done
independently after understanding how Laravel connects to databases.

### 2. Evaluation Logic (Core Feature)
The evaluate() method pattern on the Question model was designed
independently. The goal was to avoid putting if/else chains in the
controller for each question type. The match expression delegates
evaluation to private methods per type — this was a deliberate
architectural decision made while planning the data flow.

### 3. Controllers
QuizController and AttemptController were written manually. The
saveOptions() method was structured to handle all question types
in one place. The score calculation loop in AttemptController was
written and tested independently — verified that marks are only
awarded when evaluate() returns true.

### 4. Routing & Middleware
All routes in web.php were written manually after understanding
Laravel's resource routing pattern. Named routes were used
consistently to keep views clean.

### 5. Debugging & Testing
All bugs were identified and fixed manually — including the multiple
choice option index mismatch, the storage symlink for image uploads,
and the result page answer display logic.

## Areas Where AI Assistance Was Taken

### 1. Database Schema (Partial)
Asked AI for an initial suggestion on table structure for a flexible
quiz system. The suggested schema was reviewed and modified —
added the `order` column to questions and options, changed the
answers table to store raw values as text/JSON for auditability,
and ensured foreign key constraints used cascadeOnDelete.

### 2. Blade View Templates (Partial)
Asked AI to generate initial Bootstrap 5 HTML structure for the
quiz attempt page and result page. The generated output was heavily
edited — fixed the YouTube embed parsing logic, adjusted the answer
breakdown section to show correct answers only for wrong questions,
and restructured the option rendering for each question type.

## Summary

AI was used as a reference tool in 2 out of 5 major areas of the
project, and even in those areas the output required significant
manual review and correction. The architecture, evaluation logic,
controllers, routing and all debugging was done independently.