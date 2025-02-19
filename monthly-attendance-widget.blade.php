<x-filament::widget>
    <x-filament::card>
          <div class="bg-green-500 text-white p-2">Test</div>
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Monthly Attendance Calendar</h2>

        {{-- Month & Year Display with Filter Icon --}}
        <div class="flex items-center justify-between bg-gray-100 p-2 rounded-lg mb-4">
            {{-- Month & Year Display --}}
            <div class="text-lg font-semibold text-gray-800">
                {{ Carbon\Carbon::create(null, $selectedMonth, 1)->format('F') }} {{ $selectedYear }}
            </div>

            {{-- Filter Icon (Click to Open Dropdown) --}}
            <div x-data="{ showDropdown: false }" class="relative">
                <button @click="showDropdown = !showDropdown" class="p-2 rounded-full shadow-md hover:bg-gray-300">
                    {{-- Fix: Ensure Image Path is Correct --}}
                    <img src="{{ asset('filter.svg') }}" alt="Filter" class="w-6 h-6">
                </button>

                {{-- Dropdown Form --}}
                <div x-show="showDropdown" @click.away="showDropdown = false"
                     class="absolute right-0 mt-2 bg-white border border-gray-300 shadow-md rounded p-4 w-48 hidden">
                    <div class="flex flex-col space-y-2">
                        <label class="text-sm font-semibold">Month:</label>
                        <select wire:model.defer="selectedMonth" class="border rounded px-3 py-1">
                            @foreach (range(1, 12) as $m)
                                <option value="{{ $m }}">{{ Carbon\Carbon::create(null, $m, 1)->format('F') }}</option>
                            @endforeach
                        </select>

                        <label class="text-sm font-semibold">Year:</label>
                        <select wire:model.defer="selectedYear" class="border rounded px-3 py-1">
                            @foreach (range(Carbon\Carbon::now()->year - 5, Carbon\Carbon::now()->year + 1) as $y)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endforeach
                        </select>

                        {{-- Submit Button to Apply Filter --}}
                        <button wire:click="applyFilter" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-700 mt-2">Submit</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Active Filter Indicator (Only Shows When Filter is Applied) --}}
        @if ($selectedMonth != now()->month || $selectedYear != now()->year)
            <div class="mt-3 text-sm font-semibold text-gray-700 text-center">
                Viewing: {{ Carbon\Carbon::create(null, $selectedMonth, 1)->format('F') }} {{ $selectedYear }}  
                <span class="bg-green-200 text-green-800 px-2 py-1 rounded text-xs ml-2">Filtered</span>
            </div>
        @endif

        {{-- Scrollable Calendar Section --}}
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 text-center text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 border text-gray-700 font-bold">Employee</th>
                        <th class="p-2 border text-gray-700 font-bold">Avg. Arrival Time</th>
                        <th class="p-2 border text-gray-700 font-bold">Avg. Work Hours</th>
                        @foreach ($calendar->filter() as $day)
                            <th class="p-2 border text-gray-700 font-bold">
                                {{ $day['date']->format('d') }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        {{-- Employee Details --}}
                        <td class="p-2 border text-gray-700 font-bold">
                            {{ $employeeName }} <br>
                            <span class="text-xs text-gray-500">{{ $employeeCode }}</span>
                        </td>

                        {{-- Average Arrival Time --}}
                        <td class="p-2 border text-gray-700 font-bold tooltip">
                            {{ $averageArrivalTime }}
                            <span class="tooltip-text">Timezone: Asia/Kolkata</span>
                        </td>

                        {{-- Average Work Hours --}}
                        <td class="p-2 border text-gray-700 font-bold">
                            {{ number_format($averageWorkHours, 2) }} hrs
                        </td>

                      {{-- Attendance Data for Each Day --}}
@foreach ($calendar->filter() as $day)
    <td class="p-2 border rounded-lg relative
        @class([
            'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200' => $day['isWeekend'],  // Weekend (Light & Dark Mode)
            'bg-primary-100 text-primary-700' => $day['isHoliday'],  // Filament Primary Color for Holidays
            'bg-success-100 text-success-700' => $day['status'] == 'present',  // Filament Success for Present
            'bg-danger-100 text-danger-700' => $day['status'] == 'absent',  // Filament Danger for Absent
            'bg-warning-100 text-warning-700' => $day['status'] == 'pending'  // Filament Warning for Pending
        ])">

        {{-- Weekend --}}
        @if($day['isWeekend'])
            <div class="tooltip">
                <span class="font-bold">{{ $day['date']->format('D') }}</span>
                <span class="tooltip-text">
                    {{ $day['date']->format('l') }} {{-- Full day name (Saturday/Sunday) --}}
                </span>
            </div>

        {{-- Holiday (Show "H" and Name on Hover) --}}
        @elseif($day['isHoliday'])
            <div class="tooltip">
                <span class="text-sm font-bold block">H</span>
                <span class="tooltip-text">{{ $day['holidayName'] }}</span>
            </div>

        {{-- Absent (Show "-" and "Absent" on Hover) --}}
        @elseif($day['status'] == 'absent')
            <div class="tooltip">
                <span class="text-sm font-bold block">-</span>
                <span class="tooltip-text">Absent</span>
            </div>

        {{-- Present (Show Total Work Hours and Arrival Time on Hover) --}}
        @else
            <div class="tooltip">
                <span class="text-sm font-bold block">
                    {{ number_format($day['totalWorkHours'] ?? 0, 2) }} hrs
                </span>
                <span class="tooltip-text">
                    Arrival:
                    @if (!empty($day['arrivalTime']) && $day['arrivalTime'] !== 'N/A')
                        {{ \Carbon\Carbon::parse($day['arrivalTime'])->timezone('Asia/Kolkata')->format('h:i A') }}
                    @else
                        N/A
                    @endif
                </span>
            </div>
        @endif
    </td>
@endforeach
                    </tr>
                </tbody>
            </table>
          

        </div>

        {{-- Tooltip Styling (CSS) --}}
        <style>
            .tooltip {
                position: relative;
                display: inline-block;
                cursor: pointer;
            }

            .tooltip .tooltip-text {
                visibility: hidden;
                width: 130px;
                background-color: black;
                color: white;
                text-align: center;
                padding: 5px;
                border-radius: 5px;
                position: absolute;
                z-index: 1;
                bottom: 125%;
                left: 50%;
                transform: translateX(-50%);
                opacity: 0;
                transition: opacity 0.3s;
                font-size: 12px;
            }

            .tooltip:hover .tooltip-text {
                visibility: visible;
                opacity: 1;
            }
        </style>

    </x-filament::card>
</x-filament::widget>
