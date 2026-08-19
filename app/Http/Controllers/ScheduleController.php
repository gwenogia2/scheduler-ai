<?php

namespace App\Http\Controllers;

use App\Agents\ScheduleAgent;
use App\Models\Schedule;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    public function index()
    {
        $tasks = Task::orderBy('deadline', 'asc')->get();

        $schedules = Schedule::with('task')
            ->orderBy('scheduled_date', 'asc')
            ->get();

        return view('dashboard', compact('tasks', 'schedules'));
    }

    public function storeTask(Request $request)
    {
        $validated = $request->validate([
            'task_name'       => 'required|string|max:255',
            'deadline'        => 'required|date|after:now',
            'estimated_hours' => 'required|integer|min:1|max:40',
            'difficulty'      => 'required|in:easy,medium,hard',
        ]);

        Task::create($validated);

        return redirect()->back()->with('success', 'Tugas berhasil ditambahkan!');
    }

    public function generate()
    {
        $pendingTasks = Task::where('status', 'pending')
            ->orderBy('deadline', 'asc')
            ->get();

        if ($pendingTasks->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada tugas aktif (pending) untuk dijadwalkan.');
        }

        $tasksPayload = $pendingTasks->map(function ($task) {
            return [
                'id'         => $task->id,
                'name'       => $task->task_name,
                'deadline'   => $task->deadline->format('Y-m-d H:i'),
                'hours'      => $task->estimated_hours,
                'difficulty' => $task->difficulty,
            ];
        })->toJson();

        try {
            $agent = new ScheduleAgent($tasksPayload);
            $response = $agent->prompt('Buatkan rekomendasi jadwal.');

            $cleanJson = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', trim((string) $response));
            $schedulesData = json_decode($cleanJson, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($schedulesData)) {
                return redirect()->back()->with('error', 'Gagal memproses respons format dari AI.');
            }

            DB::transaction(function () use ($schedulesData) {
                Schedule::truncate();

                foreach ($schedulesData as $item) {
                    if (isset($item['task_id'], $item['scheduled_date'], $item['time_slot'])) {
                        Schedule::create([
                            'task_id'             => $item['task_id'],
                            'scheduled_date'     => $item['scheduled_date'],
                            'time_slot'          => $item['time_slot'],
                            'recommendation_note' => $item['recommendation_note'] ?? null,
                        ]);
                    }
                }
            });

            return redirect()->back()->with('success', 'Rekomendasi jadwal AI berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem/AI: ' . $e->getMessage());
        }
    }
}
