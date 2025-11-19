<?php

namespace Nalrep\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Nalrep\Facades\Nalrep; // We haven't created the Facade yet, but we will
use Nalrep\NalrepManager;

class ReportController extends Controller
{
    public function generate(Request $request)
    {
        $allowedFormats = config('nalrep.allowed_formats', ['html', 'json']);
        $allowedFormatsString = implode(',', $allowedFormats);

        $request->validate([
            'prompt' => 'required|string|max:1000',
            'format' => 'nullable|string|in:' . $allowedFormatsString,
        ]);

        $prompt = $request->input('prompt');
        $format = $request->input('format', 'html');

        // For now, we instantiate the manager directly if Facade is not ready
        // In real app, we use dependency injection
        $manager = app('nalrep');
        
        // Use the manager to generate the report
        try {
            $report = $manager->generate($prompt, $format);
        } catch (\Exception $e) {
            $report = "Error generating report: " . $e->getMessage();
        }

        if ($request->wantsJson() || $format === 'json') {
            return response()->json(['report' => $report]);
        }

        return view('nalrep::result', ['report' => $report]);
    }
}
