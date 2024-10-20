<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index()
    {
        // Get all log files in the storage/logs directory
        $files = File::files(storage_path('logs'));

        // Filter the files to only show .log files
        $logFiles = array_filter($files, function ($file) {
            return $file->getExtension() === 'log';
        });

        return view('logs.index', compact('logFiles'));
    }

    /**
     * Display the contents of a specific log file.
     */
    public function show($filename)
    {
        $filePath = storage_path("logs/{$filename}");

        // Check if the file exists
        if (!File::exists($filePath)) {
            return redirect('/logs')->withErrors('File not found.');
        }

        // Get the content of the log file
        $logContent = File::get($filePath);

        return view('logs.show', compact('filename', 'logContent'));
    }

    /**
     * Delete a specific log file.
     */
    public function destroy($filename)
    {
        $filePath = storage_path("logs/{$filename}");

        // Check if the file exists
        if (!File::exists($filePath)) {
            return redirect('/logs')->withErrors('File not found.');
        }

        // Delete the log file
        File::delete($filePath);

        return redirect('/logs')->with('success', 'Log file deleted successfully.');
    }
}
