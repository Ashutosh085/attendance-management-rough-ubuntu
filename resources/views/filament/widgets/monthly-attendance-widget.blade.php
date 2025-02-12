<x-filament-widgets::widget>
    <x-filament::section>
    <div class="bg-white shadow rounded-lg p-4">
    <h3 class="text-lg font-bold">Monthly Attendance Calendar</h3>

    <div class="flex justify-between mt-2">
        <p class="text-sm text-gray-600">Active filters: <strong>{{ now()->format('F Y') }}</strong></p>
        <input type="text" placeholder="Search" class="border rounded px-2 py-1 text-sm">
    </div>

    <div class="mt-3">
        <p class="text-sm text-gray-600">Avg. Arrival Time:</p>
        <p class="text-lg font-bold text-green-600">{{ $averageArrivalTime ?? 'N/A' }}</p>
    </div>

    <div class="mt-3">
        <p class="text-sm text-gray-600">Avg. Work Hours:</p>
        <div class="flex space-x-2">
            @foreach ($days as $day)
                <div class="text-center p-2 rounded bg-gray-100">
                    <p class="text-sm font-semibold">{{ $day }}</p>
                    <p class="text-xs">{{ $workHours[$day] ?? '-' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>


    </x-filament::section>
</x-filament-widgets::widget>
