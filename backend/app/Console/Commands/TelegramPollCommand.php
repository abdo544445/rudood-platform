<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Channel;
use App\Models\Workspace;
use App\Http\Controllers\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TelegramPollCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:poll {--workspace= : Specific workspace ID} {--token= : Specific bot token} {--once : Run single cycle and exit}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Polls Telegram for incoming messages and triggers the AI automation engine (essential for localhost development)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🤖 Rudood Telegram Live Long-Polling Service Started...');

        $workspaceId = $this->option('workspace');
        $tokenOption = $this->option('token');
        $runOnce     = $this->option('once');

        // Find relevant channels
        $query = Channel::where('platform', 'telegram');
        if ($workspaceId) {
            $query->where('workspace_id', $workspaceId);
        }
        if ($tokenOption) {
            $query->where('bot_token', $tokenOption);
        }

        $channels = $query->get();

        if ($channels->isEmpty()) {
            // Fallback: check if we have any workspace
            $firstWs = Workspace::first();
            if ($tokenOption && $firstWs) {
                $channel = Channel::updateOrCreate(
                    ['workspace_id' => $firstWs->id, 'platform' => 'telegram'],
                    ['bot_token' => $tokenOption, 'is_connected' => true]
                );
                $channels = collect([$channel]);
            } else {
                $this->warn('No connected Telegram channels found. Connect one in Settings > Channels or pass --token=<YOUR_BOT_TOKEN>');
                return 1;
            }
        }

        $webhookController = app(WebhookController::class);
        $offsets = [];

        // First step: delete any invalid localhost webhook on Telegram so getUpdates works
        foreach ($channels as $channel) {
            if ($channel->bot_token) {
                try {
                    $delRes = Http::timeout(10)->post("https://api.telegram.org/bot{$channel->bot_token}/deleteWebhook");
                    $botInfo = Http::timeout(10)->get("https://api.telegram.org/bot{$channel->bot_token}/getMe")->json('result');
                    $this->info("✓ Hook cleared for @{$botInfo['username']} (ID: {$channel->id}, Workspace #{$channel->workspace_id})");
                } catch (\Throwable $e) {
                    $this->warn("Failed to reset webhook: " . $e->getMessage());
                }
            }
        }

        $this->info("⚡ Listening for incoming Telegram messages in real-time... (Press Ctrl+C to stop)");

        do {
            foreach ($channels as $channel) {
                $token = $channel->bot_token;
                if (!$token) continue;

                $offset = $offsets[$channel->id] ?? 0;

                try {
                    $response = Http::timeout(15)->get("https://api.telegram.org/bot{$token}/getUpdates", [
                        'offset'  => $offset,
                        'timeout' => 5,
                    ]);

                    if ($response->successful() && ($response->json('ok') ?? false)) {
                        $updates = $response->json('result') ?? [];

                        foreach ($updates as $update) {
                            $updateId = $update['update_id'] ?? 0;
                            $offsets[$channel->id] = $updateId + 1;

                            $message = $update['message'] ?? null;
                            if (!$message || !isset($message['text'])) {
                                continue;
                            }

                            $sender = $message['from']['first_name'] ?? ($message['from']['username'] ?? 'User');
                            $text   = $message['text'] ?? '';
                            $chatId = $message['chat']['id'] ?? '';

                            $this->line("\n📩 \033[36m[Incoming Telegram Message]\033[0m from \033[33m{$sender}\033[0m (Chat ID: {$chatId}): \033[1m\"{$text}\"\033[0m");

                            // Simulate request into WebhookController
                            $fakeRequest = new Request($update);
                            $webhookRes = $webhookController->handleTelegram($fakeRequest, $channel->workspace_id);

                            $this->info("  ↳ ✓ Processed & AI response dispatched back to Telegram!");
                        }
                    }
                } catch (\Throwable $e) {
                    $this->error("Polling error for channel #{$channel->id}: " . $e->getMessage());
                }
            }

            if ($runOnce) break;
            usleep(500000); // 0.5s pause
        } while (true);

        return 0;
    }
}
