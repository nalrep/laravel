<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nalrep Report Result</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="antialiased bg-gray-100 min-h-screen flex flex-col items-center justify-center p-6">
    <div class="w-full max-w-4xl bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gray-800 p-4 flex justify-between items-center">
            <h1 class="text-xl font-bold text-white">Report Result</h1>
            <a href="/reports" class="text-gray-300 hover:text-white text-sm">&larr; New Report</a>
        </div>

        <div class="p-6 overflow-x-auto">
            @if(isset($format) && $format === 'pdf' && isset($pdfData))
                {{-- PDF Preview --}}
                <div class="space-y-4">
                    <div class="flex items-center justify-between bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <div>
                                <h3 class="font-semibold text-gray-900">PDF Report Generated</h3>
                                <p class="text-sm text-gray-500">Preview below or download to save</p>
                            </div>
                        </div>
                        <a href="data:application/pdf;base64,{{ $pdfData }}" download="report-{{ date('Y-m-d-His') }}.pdf"
                            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download PDF
                        </a>
                    </div>

                    {{-- PDF Viewer --}}
                    <div class="border border-gray-300 rounded-lg overflow-hidden bg-gray-100" style="height: 800px;">
                        <embed src="data:application/pdf;base64,{{ $pdfData }}" type="application/pdf" width="100%"
                            height="100%" class="rounded-lg" />
                    </div>
                </div>
            @else
                {{-- HTML/JSON Report --}}
                {!! $report !!}
            @endif
        </div>

        <div class="bg-gray-50 p-4 border-t border-gray-200 flex justify-end">
            @if(!isset($format) || $format !== 'pdf')
                <button onclick="window.print()"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Print / Save as PDF
                </button>
            @endif
        </div>
    </div>
</body>

</html>