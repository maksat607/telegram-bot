<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Log File - {{ $filename }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .log-content {
            white-space: pre-wrap;
            background-color: #f5f5f5;
            padding: 20px;
            border: 1px solid #ddd;
            overflow-x: auto;
        }
        a {
            display: inline-block;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<h1>Viewing Log File: {{ $filename }}</h1>

<a href="{{ url('/logs') }}">Back to Logs List</a>

<div class="log-content">
    {!! nl2br(e($logContent)) !!}
</div>
</body>
</html>
