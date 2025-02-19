<x-filament::widget>
    <x-filament::card>
        <div class="text-center">
            <h3 class="text-lg font-semibold text-gray-700">
                {{ $isClockedIn ? 'CLICK/TAP TO CLOCK OUT' : 'CLICK/TAP TO CLOCK IN' }}
            </h3>

            <div class="flex justify-center my-4">
                <div class="w-16 h-16 rounded-full flex items-center justify-center 
                    {{ $isClockedIn ? 'bg-red-100' : 'bg-green-100' }}">
                    <svg class="w-8 h-8 {{ $isClockedIn ? 'text-red-500' : 'text-green-500' }}" 
                         xmlns="http://www.w3.org/2000/svg" fill="none" 
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <x-filament::button wire:click="toggleClock" color="{{ $isClockedIn ? 'danger' : 'success' }}">
                {{ $isClockedIn ? 'Clock Out' : 'Clock In' }}
            </x-filament::button>

            <p class="text-sm text-gray-500 mt-2">
                Clocked in at: <strong>{{ $clockInTime }}
            </p>
            <p></strong> Using Device: <strong>Chrome/132.0.0.0</strong></p>

            <div class="mt-4">
                <p class="text-sm text-gray-600">Your average time of arrival for {{ now()->format('Y-m') }}:</p>
                <p class="text-lg font-semibold text-green-600 bg-green-100 px-3 py-1 inline-block rounded-md">
                    {{ $averageArrivalTime }}
                </p>
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>
