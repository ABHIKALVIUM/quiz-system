<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Quiz System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; }
        .navbar { background: #2c3e50 !important; }
        .navbar-brand { color: #fff !important; font-weight: 700; font-size: 1.4rem; }
        .card { border: none; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .btn-primary { background: #2c3e50; border-color: #2c3e50; }
        .btn-primary:hover { background: #1a252f; border-color: #1a252f; }
        .badge-type { font-size: 0.75rem; padding: 4px 10px; border-radius: 20px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('quizzes.index') }}">📝 Quiz System</a>
        <div class="ms-auto">
            <a href="{{ route('quizzes.index') }}" class="btn btn-outline-light btn-sm me-2">All Quizzes</a>
            <a href="{{ route('quizzes.create') }}" class="btn btn-light btn-sm">+ New Quiz</a>
        </div>
    </div>
</nav>
<div class="container pb-5">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @yield('content')
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>