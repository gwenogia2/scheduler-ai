<?php

namespace App\Agents;

use Illuminate\Broadcasting\Channel;
use Laravel\Ai\Ai;
use Laravel\Ai\Approvals\Decisions;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Providers\TextProvider;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Prompts\AgentPrompt;
use Laravel\Ai\Responses\AgentResponse;
use Laravel\Ai\Responses\QueuedAgentResponse;
use Laravel\Ai\Responses\StreamableAgentResponse;
use Stringable;

class ScheduleAgent implements Agent
{
    public function __construct(public string $tasksPayload = '') {}

    public function instructions(): Stringable|string
    {
        return "Kamu adalah sistem penjadwalan cerdas untuk mahasiswa.
Diberikan daftar tugas aktif berikut dalam format JSON:
{$this->tasksPayload}

Tugasmu:
1. Buatkan rekomendasi alokasi waktu belajar harian (time-blocking) secara fleksibel dan realistis.
2. Batasi waktu belajar maksimal 6 jam per hari agar mahasiswa tidak mengalami burnout.
3. Utamakan tugas dengan deadline lebih dekat dan tingkat kesulitan lebih tinggi ('hard').

Keluarkan output HANYA berupa JSON array valid tanpa format Markdown/Code Block, dengan skema persis berikut:
[
  {
    \"task_id\": 1,
    \"scheduled_date\": \"YYYY-MM-DD\",
    \"time_slot\": \"HH:MM - HH:MM\",
    \"recommendation_note\": \"Alasan singkat penempatan jadwal ini\"
  }
]";
    }

    public function prompt(
        Decisions|string $prompt = '',
        array $attachments = [],
        Lab|array|string|null $provider = null,
        ?string $model = null,
        ?int $timeout = null,
    ): AgentResponse {
        $resolvedProvider = $provider instanceof TextProvider
            ? $provider
            : Ai::driver(is_string($provider) ? $provider : null);

        if (is_string($resolvedProvider)) {
            $resolvedProvider = Ai::driver($resolvedProvider);
        }

        /** @var TextProvider $textProvider */
        $textProvider = $resolvedProvider;

        return Ai::prompt(new AgentPrompt(
            $this,
            (string) $prompt,
            $attachments,
            $textProvider,
            $model,
            $timeout
        ));
    }

    public function stream(
        Decisions|string $prompt = '',
        array $attachments = [],
        Lab|array|string|null $provider = null,
        ?string $model = null,
        ?int $timeout = null,
    ): StreamableAgentResponse {
        throw new \BadMethodCallException('Method stream() tidak diimplementasikan.');
    }

    public function queue(
        Decisions|string $prompt = '',
        array $attachments = [],
        Lab|array|string|null $provider = null,
        ?string $model = null
    ): QueuedAgentResponse {
        throw new \BadMethodCallException('Method queue() tidak diimplementasikan.');
    }

    public function broadcast(
        Decisions|string $prompt = '',
        Channel|array $channels = [],
        array $attachments = [],
        bool $now = false,
        Lab|array|string|null $provider = null,
        ?string $model = null
    ): StreamableAgentResponse {
        throw new \BadMethodCallException('Method broadcast() tidak diimplementasikan.');
    }

    public function broadcastNow(
        Decisions|string $prompt = '',
        Channel|array $channels = [],
        array $attachments = [],
        Lab|array|string|null $provider = null,
        ?string $model = null
    ): StreamableAgentResponse {
        throw new \BadMethodCallException('Method broadcastNow() tidak diimplementasikan.');
    }

    public function broadcastOnQueue(
        Decisions|string $prompt = '',
        Channel|array $channels = [],
        array $attachments = [],
        Lab|array|string|null $provider = null,
        ?string $model = null
    ): QueuedAgentResponse {
        throw new \BadMethodCallException('Method broadcastOnQueue() tidak diimplementasikan.');
    }
}