<div class="p-4 bg-white rounded-lg shadow-md w-72">
    <div class="text-center">
        @if ($isClockedIn)
            <h3 class="text-lg font-semibold text-gray-700">CLICK/TAP TO CLOCK OUT</h3>
            <div class="flex justify-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <button 
                wire:click="toggleClock"
                class="mt-2 px-4 py-2 bg-red-500 text-black rounded transition hover:bg-red-600">
                Clock Out
            </button>
        @else
            <h3 class="text-lg font-semibold text-gray-700">CLICK/TAP TO CLOCK IN</h3>
            <div class="flex justify-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <button 
                wire:click="toggleClock"
                class="mt-2 px-4 py-2 bg-green-500 text-black rounded transition hover:bg-green-600">
                Clock In
            </button>
        @endif

        <p class="text-sm text-gray-500 mt-2">
            Clocked in at: <strong>{{ $clockInTime }}</strong> using Device: <strong>{{ $device }}</strong>
        </p>

        <div class="mt-4">
            <p class="text-sm text-gray-600">Your average time of arrival for {{ now()->format('Y-m') }}:</p>
            <p class="text-lg font-semibold text-green-600 bg-green-100 px-3 py-1 inline-block rounded-md">
                {{ $averageArrivalTime }}
            </p>
        </div>
    </div>
</div>


