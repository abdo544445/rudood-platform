<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bot;
use App\Models\AutoRule;
use App\Models\KnowledgeBase;
use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpWord\IOFactory as WordFactory;

class BotController extends Controller
{
    /**
     * Get the workspace's bot (or fail gracefully).
     */
    private function getBot(): Bot
    {
        return Bot::where('workspace_id', auth()->user()->workspace_id)->firstOrFail();
    }

    /**
     * Show the AI Management page with all saved rules and documents.
     */
    public function manageView()
    {
        $bot   = $this->getBot();
        $rules = AutoRule::where('workspace_id', auth()->user()->workspace_id)
                         ->orderByDesc('created_at')
                         ->get();
        $docs  = KnowledgeBase::where('bot_id', $bot->id)
                               ->orderByDesc('created_at')
                               ->get();

        return view('ai-manage', compact('bot', 'rules', 'docs'));
    }

    /**
     * Save/update main bot settings (name, system prompt, welcome message, tone).
     */
    public function saveBot(Request $request)
    {
        $request->validate([
            'name'            => 'nullable|string|max:255',
            'bot_name'        => 'nullable|string|max:255',
            'system_prompt'   => 'nullable|string',
            'welcome_message' => 'nullable|string',
            'bot_tone'        => 'nullable|in:formal,friendly,sales',
        ]);

        $bot = $this->getBot();

        $name = $request->input('name') ?? $request->input('bot_name') ?? $bot->name;

        $bot->update([
            'name'            => $name,
            'system_prompt'   => $request->input('system_prompt', $bot->system_prompt),
            'welcome_message' => $request->input('welcome_message', $bot->welcome_message),
            'bot_tone'        => $request->input('bot_tone', $bot->bot_tone),
        ]);

        return back()->with('status', 'تم حفظ إعدادات البوت بنجاح ✓');
    }

    // ─── FAQ / Auto-Rules ─────────────────────────────────────────────────────

    /**
     * Save a new FAQ rule (question + answer).
     */
    public function saveRule(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:500',
            'answer'   => 'required|string',
        ]);

        AutoRule::create([
            'workspace_id'     => auth()->user()->workspace_id,
            'question'         => $request->question,
            'keywords'         => explode(' ', strtolower($request->question)), // auto-extract keywords
            'trigger_condition'=> 'contains',
            'reply_template'   => $request->answer,
            'is_active'        => true,
        ]);

        return back()->with('status', 'تم حفظ السؤال والإجابة بنجاح ✓');
    }

    /**
     * Delete a specific FAQ rule.
     */
    public function deleteRule(int $id)
    {
        $rule = AutoRule::where('id', $id)
                        ->where('workspace_id', auth()->user()->workspace_id)
                        ->firstOrFail();
        $rule->delete();

        return back()->with('status', 'تم حذف القاعدة بنجاح ✓');
    }

    // ─── Document Upload & Extraction ─────────────────────────────────────────

    /**
     * Upload a PDF / DOCX / TXT file, extract its text, and save to knowledge_bases.
     */
    public function uploadDocument(Request $request)
    {
        $request->validate([
            'doc_file' => 'required|file|mimes:pdf,docx,doc,txt|max:15360', // 15 MB
        ]);

        $bot  = $this->getBot();
        $file = $request->file('doc_file');
        $ext  = strtolower($file->getClientOriginalExtension());
        $name = $file->getClientOriginalName();

        // Store the file under storage/app/knowledge/{workspace_id}/
        $path = $file->store('knowledge/' . auth()->user()->workspace_id, 'local');

        // Extract raw text based on file type
        $text = match($ext) {
            'pdf'   => $this->extractPdf(storage_path('app/' . $path)),
            'docx', 'doc' => $this->extractWord(storage_path('app/' . $path)),
            'txt'   => file_get_contents(storage_path('app/' . $path)),
            default => '',
        };

        KnowledgeBase::create([
            'bot_id'        => $bot->id,
            'file_name'     => $name,
            'file_path'     => $path,
            'document_text' => $text,
            'status'        => 'processed',
        ]);

        return back()->with('status', "تم رفع وتحليل الملف «{$name}» بنجاح ✓");
    }

    /**
     * Delete a knowledge base document.
     */
    public function deleteDocument(int $id)
    {
        $bot = $this->getBot();
        $doc = KnowledgeBase::where('id', $id)->where('bot_id', $bot->id)->firstOrFail();
        \Storage::disk('local')->delete($doc->file_path);
        $doc->delete();

        return back()->with('status', 'تم حذف الملف بنجاح ✓');
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    private function extractPdf(string $filePath): string
    {
        try {
            $parser   = new PdfParser();
            $pdf      = $parser->parseFile($filePath);
            return $pdf->getText();
        } catch (\Exception $e) {
            return '';
        }
    }

    private function extractWord(string $filePath): string
    {
        try {
            $phpWord  = WordFactory::load($filePath);
            $sections = $phpWord->getSections();
            $text     = '';
            foreach ($sections as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . "\n";
                    }
                    if (method_exists($element, 'getElements')) {
                        foreach ($element->getElements() as $child) {
                            if (method_exists($child, 'getText')) {
                                $text .= $child->getText() . ' ';
                            }
                        }
                        $text .= "\n";
                    }
                }
            }
            return $text;
        } catch (\Exception $e) {
            return '';
        }
    }

    // Legacy API method kept for backwards compat
    public function index()
    {
        $bots = Bot::where('workspace_id', auth()->user()->workspace_id)->get();
        return response()->json(['success' => true, 'data' => $bots]);
    }
}
