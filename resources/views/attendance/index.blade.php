<x-app-layout>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/datepicker.min.js"></script>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Attendance') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="px-6 pt-6 mb-5 md:w-1/2 2xl:w-1/2">
                    @if (request('search'))
                        <h2 class="pb-3 text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                            Search results for: {{ request('search') }}
                        </h2>
                    @endif
                    <form class="flex items-center gap-2">
                        @can('admin')
                            <x-text-input id="search" name="search" type="text" class="w-full"
                                placeholder="Search by name or status ..." value="{{ request('search') }}" autofocus />
                        @endcan
                        <!-- Input field for searching by date -->
                        <x-text-input id="searchDate" name="searchDate" type="date" class="w-full"
                            placeholder="Search by attendance date ..." value="{{ request('searchDate') }}" autofocus />

                        <x-primary-button type="submit">
                            {{ __('Search') }}
                        </x-primary-button>

                        @can('admin')
                            <x-create-button href="{{ route('attendance.create') }}"></x-create-button>
                        @endcan
                        <x-primary-button class="bg-white sm:rounded-lg flex items-center gap-2 whitespace-nowrap">
                            <a href="{{ route('attendance.exportpdf', ['search' => request('search'), 'searchDate' => request('searchDate')]) }}"
                                class="btn btn-sm btn-success">Export PDF</a>
                        </x-primary-button>
                    </form>
                </div>
                <div class="px-6 text-xl text-gray-900 dark:text-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            @if (session('success'))
                                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)"
                                    class="pb-3 text-sm text-green-600 dark:text-green-400">{{ session('success') }}
                                </p>
                            @endif
                            @if (session('info'))
                                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)"
                                    class="pb-3 text-sm text-green-600 dark:text-green-400">{{ session('info') }}
                                </p>
                            @endif
                            @if (session('danger'))
                                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)"
                                    class="pb-3 text-sm text-red-600 dark:text-red-400">{{ session('danger') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    Name
                                </th>
                                <th scope="col" class="hidden px-6 py-3 md:block">
                                    Email
                                </th>
                                <th scope="col" class="py-3">
                                    Attendance Date
                                </th>
                                <th scope="col" class="py-3">
                                    Attendance Time
                                </th>
                                <th scope="col" class="py-3">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($attendances as $attendance)
                                <tr class="odd:bg-white odd:dark:bg-gray-800 even:bg-gray-50 even:dark:bg-gray-700">
                                    <td
                                        class="px-6 py-4 font-medium text-gray-900 md:whitespace-nowrap dark:text-white">
                                        <p>{{ $attendance->user->name }}</p>
                                    </td>
                                    <td class="hidden px-6 py-4 md:block">
                                        <p>{{ $attendance->user->email }}</p>
                                    </td>
                                    <td>
                                        <p>{{ $attendance->created_at->format('D, d F Y') }}</p>
                                        <!--format tampilan attendance_date-->
                                    </td>
                                    @if ($attendance->created_at->format('H:i:s') <= '08:00:00')
                                        <td class='text-green-500 dark:text-green-500'>
                                            <p>{{ $attendance->created_at->format('H : i : s') }}</p>
                                        </td>
                                    @else
                                        <td class="text-red-500 dark:text-red-500">
                                            <p>{{ $attendance->created_at->format('H : i : s') }}</p>
                                        </td>
                                    @endif
                                    @if ($attendance->status == 'izin')
                                        <td class="text-blue-500">
                                            <p class="uppercase">{{ $attendance->status }}</p>
                                        </td>
                                    @elseif ($attendance->status == 'sakit')
                                        <td class="text-yellow-500 dark:text-yellow-500">
                                            <p class="uppercase">{{ $attendance->status }}</p>
                                        </td>
                                    @elseif ($attendance->status == 'hadir')
                                        <td class="text-green-500 dark:text-green-500">
                                            <p class="uppercase">{{ $attendance->status }}</p>
                                        </td>
                                    @else
                                        <td class="text-red-500 dark:text-red-500">
                                            <p class="uppercase">{{ $attendance->status }}</p>
                                        </td>
                                    @endif
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-3">
                                            {{-- Action here --}}
                                            @can('admin')
                                                @if ($attendance->status == 'hadir')
                                                    {{-- <form method="Post">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="text-green-500 dark:text-green-500 whitespace-nowrap">
                                                        Hadir
                                                    </button>
                                                </form> --}}
                                                    <form action="{{ route('attendance.sakit', $attendance) }}"
                                                        method="Post">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                            class="text-yellow-500 dark:text-yellow-500 whitespace-nowrap">
                                                            Sakit
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('attendance.izin', $attendance) }}"
                                                        method="Post">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                            class="text-blue-500 dark:text-blue-500 whitespace-nowrap">
                                                            Izin
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('attendance.absen', $attendance) }}"
                                                        method="Post">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                            class="text-red-500 dark:text-red-500 whitespace-nowrap">
                                                            Absen
                                                        </button>
                                                    </form>
                                                @elseif ($attendance->status == 'sakit')
                                                    <form action="{{ route('attendance.hadir', $attendance) }}"
                                                        method="Post">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                            class="text-green-500 dark:text-green-500 whitespace-nowrap">
                                                            Hadir
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('attendance.izin', $attendance) }}"
                                                        method="Post">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                            class="text-blue-500 dark:text-blue-500 whitespace-nowrap">
                                                            Izin
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('attendance.absen', $attendance) }}"
                                                        method="Post">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                            class="text-red-500 dark:text-red-500 whitespace-nowrap">
                                                            Absen
                                                        </button>
                                                    </form>
                                                @elseif ($attendance->status == 'izin')
                                                    <form action="{{ route('attendance.hadir', $attendance) }}"
                                                        method="Post">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                            class="text-green-500 dark:text-green-500 whitespace-nowrap">
                                                            Hadir
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('attendance.sakit', $attendance) }}"
                                                        method="Post">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                            class="text-yellow-500 dark:text-yellow-500 whitespace-nowrap">
                                                            Sakit
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('attendance.absen', $attendance) }}"
                                                        method="Post">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                            class="text-red-500 dark:text-red-500 whitespace-nowrap">
                                                            Absen
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('attendance.hadir', $attendance) }}"
                                                        method="Post">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                            class="text-green-500 dark:text-green-500 whitespace-nowrap">
                                                            Hadir
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('attendance.sakit', $attendance) }}"
                                                        method="Post">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                            class="text-yellow-500 dark:text-yellow-500 whitespace-nowrap">
                                                            Sakit
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('attendance.izin', $attendance) }}"
                                                        method="Post">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                            class="text-blue-500 dark:text-blue-500 whitespace-nowrap">
                                                            Izin
                                                        </button>
                                                    </form>
                                                @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr class="bg-white dark:bg-gray-800">
                                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                        Empty
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
                @if ($attendances->hasPages())
                    <div class="p-6">
                        {{ $attendances->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>


</x-app-layout>
