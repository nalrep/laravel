<div class="nalrep-container p-4 bg-white rounded shadow">
    <form action="{{ route('nalrep.generate') }}" method="POST" class="flex flex-col gap-4">
        @csrf
        <label for="nalrep-prompt" class="font-bold text-lg">Generate Report</label>
        <div class="flex gap-2">
            <input 
                type="text" 
                name="prompt" 
                id="nalrep-prompt" 
                class="flex-1 border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="e.g. Show me top customers from last month"
                required
            >
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                Generate
            </button>
        </div>
        <div class="text-sm text-gray-500">
            Powered by Narlrep AI
        </div>
    </form>
</div>
