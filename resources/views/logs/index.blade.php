<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Logs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        a {
            text-decoration: none;
        }
    </style>
</head>
<body>
<h1>Laravel Log Files</h1>

@if (session('success'))
    <div style="color: green;">{{ session('success') }}</div>
@endif

@if ($errors->any())
    <div style="color: red;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<table>
    <thead>
    <tr>
        <th>File Name</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($logFiles as $file)
        <tr>
            <td>{{ $file->getFilename() }}</td>
            <td>
                <a href="{{ url('/logs/' . $file->getFilename()) }}">View</a> |
                <form action="{{ url('/logs/' . $file->getFilename()) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="color: red; border: none; background: none;">Delete</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
