<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Narlrep Report Result</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="antialiased bg-gray-100 min-h-screen flex flex-col items-center justify-center p-6">
        <div class="w-full max-w-4xl bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gray-800 p-4 flex justify-between items-center">
                <h1 class="text-xl font-bold text-white">Report Result</h1>
                <a href="/reports" class="text-gray-300 hover:text-white text-sm">&larr; New Report</a>
            </div>
            
            <div class="p-6 overflow-x-auto">
                {!! $report !!}
            </div>

            <div class="bg-gray-50 p-4 border-t border-gray-200 flex justify-end">
                <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Print / Save as PDF
                </button>
            </div>
        </div>
    </body>
</html>
