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

        // Handle PDF format specially
        if ($format === 'pdf') {
            $pdfDisplayMode = config('nalrep.pdf_display_mode', 'inline');
            
            if ($pdfDisplayMode === 'download') {
                // Direct download
                return response($report, 200)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'attachment; filename="report-' . date('Y-m-d-His') . '.pdf"');
            } else {
                // Inline preview - convert to base64 for embedding
                $pdfBase64 = base64_encode($report);
                return view('nalrep::result', [
                    'report' => null,
                    'pdfData' => $pdfBase64,
                    'format' => 'pdf'
                ]);
            }
        }

        if ($request->wantsJson() || $format === 'json') {
            return response()->json(['report' => $report]);
        }

        return view('nalrep::result', ['report' => $report, 'format' => $format]);
    }
}
