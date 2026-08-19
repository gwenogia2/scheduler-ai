@extends('laouts.den')

@section('content')


<div class="max-w-4xl mx-auto space-y-6">
    <h1 class="text-3xl font-bold text-gray-800">AI Smart Scheduler</h1>

    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-semibold mb-4">Tambah Tugas Baru</h2>
        <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Nama Tugas</label>
                <input type="text" name="task_name" required class="w-full border p-2 rounded mt-1">
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Deadline</label>
                    <input type="datetime-local" name="deadline" required class="w-full border p-2 rounded mt-1">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Estimasi (Jam)</label>
                    <input type="number" name="estimated_hours" min="1" required class="w-full border p-2 rounded mt-1">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tingkat Kesulitan</label>
                    <select name="difficulty" class="w-full border p-2 rounded mt-1">
                        <option value="easy">Easy</option>
                        <option value="medium">Medium</option>
                        <option value="hard">Hard</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan Tugas</button>
        </form>
    </div>

    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Rekomendasi Jadwal AI</h2>
        <form action="{{ route('schedules.generate') }}" method="POST">
            @csrf
            <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded hover:bg-purple-700">Generate Jadwal</button>
        </form>
    </div>

    <div class="bg-white p-6 rounded-lg shadow overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="p-2">Tugas</th>
                    <th class="p-2">Tanggal</th>
                    <th class="p-2">Waktu Slot</th>
                    <th class="p-2">Catatan AI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schedules as $schedule)
                <tr class="border-b">
                    <td class="p-2 font-medium">{{ $schedule->task->task_name ?? 'N/A' }}</td>
                    <td class="p-2">{{ $schedule->scheduled_date->format('d M Y') }}</td>
                    <td class="p-2">{{ $schedule->time_slot }}</td>
                    <td class="p-2 text-sm text-gray-600">{{ $schedule->recommendation_note }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-4 text-center text-gray-500">Belum ada jadwal yang diproses.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


@endsection