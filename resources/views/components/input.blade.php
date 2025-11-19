@php
    $allowedFormats = config('nalrep.allowed_formats', ['html', 'json']);
@endphp

<div class="nalrep-container w-full max-w-4xl mx-auto font-sans">
    <!-- Main Card -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 transition-all duration-300">

        <!-- Header -->
        <div class="bg-white p-6 border-b border-gray-100">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-indigo-50 rounded-xl shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 tracking-tight">Nalrep Intelligence</h2>
                    <p class="text-sm text-gray-500 font-medium">Ask questions about your data in plain English</p>
                </div>
            </div>
        </div>

        <form action="{{ route('nalrep.generate') }}" method="POST" id="nalrep-form" class="p-6 pt-4">
            @csrf

            <!-- Input Area -->
            <div class="relative group mb-8">
                <label for="nalrep-prompt" class="sr-only">Your Question</label>
                <textarea name="prompt" id="nalrep-prompt" rows="3"
                    class="w-full bg-gray-50 border-0 rounded-xl p-5 text-gray-700 text-lg placeholder-gray-400 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white transition-all resize-none"
                    placeholder="Describe the report you need..." required></textarea>

                <!-- Helper Chips -->
                <div class="flex flex-wrap gap-2 mt-4 px-1">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider py-1">Try asking:</span>
                    @foreach(config('nalrep.example_prompts', []) as $index => $prompt)
                        @php
                            $colors = [
                                ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600', 'hover' => 'hover:bg-indigo-100', 'border' => 'border-indigo-100'],
                                ['bg' => 'bg-purple-50', 'text' => 'text-purple-600', 'hover' => 'hover:bg-purple-100', 'border' => 'border-purple-100'],
                                ['bg' => 'bg-pink-50', 'text' => 'text-pink-600', 'hover' => 'hover:bg-pink-100', 'border' => 'border-pink-100'],
                                ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'hover' => 'hover:bg-blue-100', 'border' => 'border-blue-100'],
                                ['bg' => 'bg-green-50', 'text' => 'text-green-600', 'hover' => 'hover:bg-green-100', 'border' => 'border-green-100'],
                            ];
                            $color = $colors[$index % count($colors)];
                        @endphp
                        <button type="button" onclick="setPrompt(this.innerText)"
                            class="text-xs font-medium {{ $color['bg'] }} {{ $color['text'] }} px-3 py-1 rounded-full {{ $color['hover'] }} transition cursor-pointer border {{ $color['border'] }}">
                            {{ $prompt }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-gray-100 pt-6">

                <!-- Format Selector -->
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <label for="nalrep-format" class="text-sm font-medium text-gray-600 whitespace-nowrap">Output
                        Format:</label>
                    <div class="relative w-full sm:w-auto">
                        <select name="format" id="nalrep-format"
                            class="w-full sm:w-auto appearance-none bg-white border border-gray-200 text-gray-700 text-sm rounded-lg pl-4 pr-10 py-2.5 focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 cursor-pointer hover:border-indigo-300 transition shadow-sm">
                            @foreach($allowedFormats as $format)
                                <option value="{{ $format }}">{{ ucfirst($format) }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Generate Button -->
                <button type="submit" id="nalrep-submit"
                    class="w-full sm:w-auto group relative inline-flex items-center justify-center px-8 py-3 text-base font-bold text-white transition-all duration-200 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg hover:shadow-indigo-500/30 transform hover:-translate-y-0.5 active:translate-y-0">
                    <span class="relative flex items-center gap-2">
                        Generate Report
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 group-hover:translate-x-1 transition-transform" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </span>
                </button>
            </div>

            <!-- Loading State -->
            <div id="nalrep-loading" class="hidden mt-8 transition-all duration-500 ease-in-out">
                <div
                    class="flex flex-col items-center justify-center p-8 border border-indigo-100 rounded-2xl bg-indigo-50/30 backdrop-blur-sm">
                    <div class="relative">
                        <div class="w-14 h-14 border-4 border-indigo-100 rounded-full animate-spin border-t-indigo-600">
                        </div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="w-2.5 h-2.5 bg-indigo-600 rounded-full shadow-lg shadow-indigo-500/50"></div>
                        </div>
                    </div>
                    <h3 class="mt-4 text-base font-semibold text-gray-900">Analyzing Request</h3>
                    <p class="text-sm text-gray-500 animate-pulse mt-1">Translating natural language to secure query...
                    </p>
                </div>
            </div>

        </form>
    </div>

    <div class="text-center mt-6">
        <p class="text-xs text-gray-400 font-medium tracking-wide">POWERED BY <span
                class="text-indigo-600 font-bold">NALREP ENGINE</span> • SECURE & READ-ONLY</p>
    </div>
</div>

<script>
    function setPrompt(text) {
        const textarea = document.getElementById('nalrep-prompt');
        textarea.value = text;
        textarea.focus();
    }

    document.getElementById('nalrep-form').addEventListener('submit', function () {
        const btn = document.getElementById('nalrep-submit');
        const loading = document.getElementById('nalrep-loading');
        const prompt = document.getElementById('nalrep-prompt');

        if (!prompt.value.trim()) return;

        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');

        // Animate button content change
        const originalContent = btn.innerHTML;
        btn.innerHTML = '<span class="flex items-center gap-2"><svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...</span>';

        loading.classList.remove('hidden');

        // Smooth scroll to loading
        setTimeout(() => {
            loading.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 100);
    });
</script>