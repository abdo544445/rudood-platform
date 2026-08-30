<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bot;
use App\Models\AutoRule;
use App\Models\KnowledgeBase;
use App\Models\Channel;

class SettingsController extends Controller
{
    private function getBot(): Bot
    {
        $workspace_id = auth()->user()->workspace_id;
        return Bot::firstOrCreate(
            ['workspace_id' => $workspace_id],
            [
                'name' => 'المساعد الذكي',
                'system_prompt' => 'أنت مساعد ذكي.',
                'model_type' => 'gemini-1.5-flash',
                'ai_provider' => 'gemini',
                'is_active' => true,
            ]
        );
    }

    public function index()
    {
        $bot = $this->getBot();
        $workspace = auth()->user()->workspace;
        $channels = Channel::where('workspace_id', auth()->user()->workspace_id)->get();
        return view('settings', compact('bot', 'workspace', 'channels'));
    }

    /**
     * Toggle the Bot active state via AJAX or POST.
     */
    public function toggleBot(Request $request)
    {
        $bot = $this->getBot();
        if ($request->has('is_active')) {
            $bot->is_active = $request->boolean('is_active');
        } else {
            $bot->is_active = !$bot->is_active;
        }
        $bot->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'   => true,
                'is_active' => (bool)$bot->is_active,
                'message'   => $bot->is_active ? 'تم تفعيل الردود التلقائية للبوت بنجاح ✓' : 'تم إيقاف الردود التلقائية للبوت مؤقتاً ⏸',
            ]);
        }

        return back()->with('status', $bot->is_active ? 'تم تفعيل الردود التلقائية للبوت بنجاح ✓' : 'تم إيقاف الردود التلقائية للبوت مؤقتاً ⏸');
    }

    /**
     * Save bot name, tone, welcome message, system prompt, and active status.
     */
    public function saveBotSettings(Request $request)
    {
        $request->validate([
            'bot_name'        => 'required|string|max:255',
            'bot_tone'        => 'required|in:formal,friendly,sales',
            'welcome_message' => 'required|string',
            'system_prompt'   => 'nullable|string',
            'is_active'       => 'nullable',
        ]);

        $bot = $this->getBot();
        $bot->update([
            'name'            => $request->bot_name,
            'bot_tone'        => $request->bot_tone,
            'welcome_message' => $request->welcome_message,
            'system_prompt'   => $request->system_prompt,
            'is_active'       => $request->has('is_active') ? $request->boolean('is_active') : $bot->is_active,
        ]);

        return back()->with('status', 'تم حفظ إعدادات وتخصيص البوت بنجاح ✓');
    }

    /**
     * Fetch available models dynamically for the selected provider.
     */
    public function fetchModels(Request $request)
    {
        $bot = $this->getBot();
        $provider = $request->input('ai_provider', 'gemini');
        $apiKey = $request->input('ai_api_key');
        $baseUrl = $request->input('api_base_url');

        $aiService = new \App\Services\AiService($bot);
        $result = $aiService->fetchAvailableModels($provider, $apiKey, $baseUrl);

        return response()->json($result);
    }

    /**
     * Save the AI provider and API key configuration directly to the bot.
     */
    public function saveAiKey(Request $request)
    {
        $bot = $this->getBot();

        $request->validate([
            'ai_provider'  => 'required|in:openai,gemini,anthropic,openai_compatible',
            'ai_api_key'   => 'nullable|string|max:500',
            'model_type'   => 'required|string|max:100',
            'api_base_url' => 'nullable|url',
            'max_tokens'   => 'nullable|integer|min:100|max:8000',
            'temperature'  => 'nullable|numeric|min:0|max:1',
        ]);

        $data = [
            'ai_provider'  => $request->ai_provider,
            'model_type'   => $request->model_type,
            'api_base_url' => $request->api_base_url,
            'max_tokens'   => $request->max_tokens ?? 500,
            'temperature'  => $request->temperature ?? 0.7,
            'api_mode'     => 'custom_byok',
        ];

        if ($request->filled('ai_api_key')) {
            $data['api_key'] = trim($request->ai_api_key);
        }

        $bot->update($data);

        return back()->with('status', 'تم حفظ مفتاح وإعدادات الذكاء الاصطناعي بنجاح في قاعدة البيانات ✓');
    }
}
