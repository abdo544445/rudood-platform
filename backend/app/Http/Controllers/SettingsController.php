<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bot;
use App\Models\AutoRule;
use App\Models\KnowledgeBase;

class SettingsController extends Controller
{
    private function getBot(): Bot
    {
        $workspace_id = auth()->user()->workspace_id;
        return Bot::where('workspace_id', $workspace_id)->firstOrFail();
    }

    public function index()
    {
        $bot = $this->getBot();
        return view('settings', compact('bot'));
    }

    /**
     * Save bot name, tone, welcome message, and system prompt.
     */
    public function saveBotSettings(Request $request)
    {
        $request->validate([
            'bot_name'        => 'required|string|max:255',
            'bot_tone'        => 'required|in:formal,friendly,sales',
            'welcome_message' => 'required|string',
            'system_prompt'   => 'nullable|string',
        ]);

        $bot = $this->getBot();
        $bot->update([
            'name'            => $request->bot_name,
            'bot_tone'        => $request->bot_tone,
            'welcome_message' => $request->welcome_message,
            'system_prompt'   => $request->system_prompt,
        ]);

        return back()->with('status', 'تم حفظ إعدادات البوت بنجاح ✓');
    }

    /**
     * Save the AI provider and API key configuration.
     */
    public function saveAiKey(Request $request)
    {
        $request->validate([
            'ai_provider' => 'required|in:openai,gemini,anthropic,openai_compatible',
            'ai_api_key'  => 'required|string',
            'model_type'  => 'required|string|max:100',
            'api_base_url'=> 'nullable|url',
            'max_tokens'  => 'nullable|integer|min:100|max:8000',
            'temperature' => 'nullable|numeric|min:0|max:1',
        ]);

        $bot = $this->getBot();
        $bot->update([
            'ai_provider'  => $request->ai_provider,
            'api_key'      => $request->ai_api_key, // goes through the setter/encryptor
            'model_type'   => $request->model_type,
            'api_base_url' => $request->api_base_url,
            'max_tokens'   => $request->max_tokens ?? 500,
            'temperature'  => $request->temperature ?? 0.7,
        ]);

        return back()->with('status', 'تم حفظ إعدادات الذكاء الاصطناعي بنجاح ✓');
    }
}
