<?php

namespace Nalrep\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Nalrep\Facades\Nalrep; // We haven't created the Facade yet, but we will

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
        } catch (\Nalrep\Exceptions\VaguePromptException $e) {
            return $this->handleError($e, $request, $format);
        } catch (\Nalrep\Exceptions\InvalidPromptException $e) {
            return $this->handleError($e, $request, $format);
        } catch (\Nalrep\Exceptions\QueryGenerationException $e) {
            return $this->handleError($e, $request, $format);
        } catch (\Nalrep\Exceptions\InvalidJsonException $e) {
            return $this->handleError($e, $request, $format);
        } catch (\Nalrep\Exceptions\ValidationException $e) {
            return $this->handleError($e, $request, $format);
        } catch (\Nalrep\Exceptions\NalrepException $e) {
            return $this->handleError($e, $request, $format);
        } catch (\Exception $e) {
            // Log unexpected errors
            \Log::error('Nalrep unexpected error: ' . $e->getMessage(), [
                'exception' => $e,
                'prompt' => $prompt,
            ]);
            
            $errorMessage = config('app.debug') 
                ? "Error: " . $e->getMessage()
                : "An unexpected error occurred. Please try again or contact support.";
            
            if ($request->wantsJson() || $format === 'json') {
                return response()->json([
                    'error' => true,
                    'message' => $errorMessage
                ], 500);
            }
            
            return view('nalrep::result', [
                'report' => '<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">' . 
                           htmlspecialchars($errorMessage) . '</div>',
                'format' => $format
            ]);
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

    /**
     * Handle Nalrep exceptions with user-friendly responses
     */
    protected function handleError(\Nalrep\Exceptions\NalrepException $e, Request $request, string $format)
    {
        $errorMessage = $e->getMessage();
        
        // Log the error for debugging
        \Log::warning('Nalrep error: ' . $errorMessage, [
            'exception' => get_class($e),
            'prompt' => $request->input('prompt'),
        ]);
        
        if ($request->wantsJson() || $format === 'json') {
            return response()->json([
                'error' => true,
                'type' => class_basename($e),
                'message' => $errorMessage
            ], 400);
        }
        
        return view('nalrep::result', [
            'report' => '<div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded">' . 
                       '<strong>Unable to generate report:</strong> ' . htmlspecialchars($errorMessage) . '</div>',
            'format' => $format
        ]);
    }
}
