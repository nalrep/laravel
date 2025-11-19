<?php

namespace Narlrep\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Narlrep\Facades\Narlrep; // We haven't created the Facade yet, but we will
use Narlrep\NarlrepManager;

class ReportController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
            'format' => 'nullable|string|in:json,html,pdf',
        ]);

        $prompt = $request->input('prompt');
        $format = $request->input('format', 'html');

        // For now, we instantiate the manager directly if Facade is not ready
        // In real app, we use dependency injection
        $manager = app('narlrep');
        
        // Use the manager to generate the report
        try {
            $report = $manager->generate($prompt, $format);
        } catch (\Exception $e) {
            $report = "Error generating report: " . $e->getMessage();
        }

        if ($request->wantsJson() || $format === 'json') {
            return response()->json(['report' => $report]);
        }

        return view('narlrep::result', ['report' => $report]);
    }
}
