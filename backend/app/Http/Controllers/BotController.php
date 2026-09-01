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
        return Bot::firstOrCreate(
            ['workspace_id' => auth()->user()->workspace_id],
            [
                'name' => 'المساعد الذكي',
                'system_prompt' => 'أنت مساعد ذكي.',
                'model_type' => 'gemini-1.5-flash',
                'ai_provider' => 'gemini',
                'is_active' => true,
            ]
        );
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

        $updateData = [
            'name'            => $name,
            'system_prompt'   => $request->input('system_prompt', $bot->system_prompt),
            'welcome_message' => $request->input('welcome_message', $bot->welcome_message),
            'bot_tone'        => $request->input('bot_tone', $bot->bot_tone),
        ];

        if ($request->has('enable_rag')) {
            $updateData['enable_rag'] = $request->boolean('enable_rag');
        }
        if ($request->has('enable_auto_rules')) {
            $updateData['enable_auto_rules'] = $request->boolean('enable_auto_rules');
        }
        if ($request->filled('api_mode')) {
            $updateData['api_mode'] = $request->input('api_mode');
        }

        $bot->update($updateData);

        return back()->with('status', 'تم حفظ إعدادات البوت بنجاح ✓');
    }

    // ─── FAQ / Auto-Rules ─────────────────────────────────────────────────────

    /**
     * Save a new FAQ rule (question + answer + custom keywords).
     */
    public function saveRule(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:500',
            'answer'   => 'required|string',
            'keywords' => 'nullable|string|max:500',
        ]);

        $keywords = [];
        if ($request->filled('keywords')) {
            $keywords = array_filter(array_map('trim', explode(',', strtolower($request->keywords))));
        }

        if (empty($keywords)) {
            // Fallback to question words after filtering out empty or single-character words
            $words = explode(' ', strtolower($request->question));
            $keywords = array_values(array_filter(array_map('trim', $words), fn($w) => mb_strlen($w) > 1));
        }

        AutoRule::create([
            'workspace_id'     => auth()->user()->workspace_id,
            'question'         => $request->question,
            'keywords'         => array_values($keywords),
            'trigger_condition'=> 'contains',
            'reply_template'   => $request->answer,
            'is_active'        => true,
        ]);

        return back()->with('status', 'تم حفظ السؤال والإجابة بنجاح ✓');
    }

    /**
     * Generate FAQ Q&A pairs from an uploaded document using AI.
     */
    public function generateFaqFromDoc(Request $request, int $id)
    {
        $bot = $this->getBot();
        $doc = KnowledgeBase::where('id', $id)->where('bot_id', $bot->id)->firstOrFail();

        if (empty(trim($doc->document_text ?? ''))) {
            return back()->with('error', 'المستند المحدد لا يحتوي على نصوص كافية للاستخراج.');
        }

        $aiService = new \App\Services\AiService($bot);
        $extractedFaqs = $aiService->extractFaqFromDocument($doc->document_text, 5);

        if (empty($extractedFaqs)) {
            return back()->with('error', 'تعذر استخراج أسئلة من هذا المستند. يرجى التأكد من مفتاح الـ API وصلاحية المستند.');
        }

        $count = 0;
        foreach ($extractedFaqs as $faq) {
            if (!empty($faq['question']) && !empty($faq['answer'])) {
                $keywords = $faq['keywords'] ?? [];
                if (empty($keywords)) {
                    $words = explode(' ', strtolower($faq['question']));
                    $keywords = array_values(array_filter(array_map('trim', $words), fn($w) => mb_strlen($w) > 1));
                }

                AutoRule::create([
                    'workspace_id'      => auth()->user()->workspace_id,
                    'question'          => $faq['question'],
                    'keywords'          => is_array($keywords) ? array_values($keywords) : [$keywords],
                    'trigger_condition' => 'contains',
                    'reply_template'    => $faq['answer'],
                    'is_active'         => true,
                ]);
                $count++;
            }
        }

        return back()->with('status', "تم توليد واستخراج ({$count}) أسئلة وأجوبة شائعة من المستند وإضافتها لقاعدة القواعد الفورية بنجاح ✓");
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
     * Upload a PDF / DOCX / TXT file, extract its text, pre-chunk it, and save to knowledge_bases.
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

        // Extract raw text based on file type  (use disk path to stay insulated from root changes)
        $absPath = \Storage::disk('local')->path($path);
        $text = match($ext) {
            'pdf'   => $this->extractPdf($absPath),
            'docx', 'doc' => $this->extractWord($absPath),
            'txt'   => file_get_contents($absPath),
            default => '',
        };

        // Pre-compute semantic chunks immediately upon upload
        $tempKb = new KnowledgeBase(['document_text' => $text]);
        $chunks = $tempKb->chunks;

        KnowledgeBase::create([
            'bot_id'        => $bot->id,
            'file_name'     => $name,
            'file_path'     => $path,
            'document_text' => $text,
            'chunks_json'   => $chunks,
            'status'        => 'processed',
        ]);

        return back()->with('status', "تم رفع وتحليل وتجزئة الملف «{$name}» إلى (" . count($chunks) . ") مقطع بحثي بنجاح ✓");
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
        // 1. Direct XML extraction from DOCX zip archive (fastest and handles all text without element type issues)
        try {
            $zip = new \ZipArchive();
            if ($zip->open($filePath) === true) {
                $xml = $zip->getFromName('word/document.xml');
                $zip->close();
                if ($xml) {
                    $dom = new \DOMDocument();
                    @$dom->loadXML($xml);
                    $paragraphs = $dom->getElementsByTagName('p');
                    $lines = [];
                    foreach ($paragraphs as $p) {
                        $pText = '';
                        $texts = $p->getElementsByTagName('t');
                        foreach ($texts as $t) {
                            $pText .= $t->nodeValue;
                        }
                        if (trim($pText) !== '') {
                            $lines[] = trim($pText);
                        }
                    }
                    $extracted = implode("\n", $lines);
                    if (!empty(trim($extracted))) {
                        return $extracted;
                    }
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Direct DOCX XML extraction failed, falling back to PhpWord: ' . $e->getMessage());
        }

        // 2. Fallback to PhpOffice\PhpWord with recursive type-safe element extraction
        try {
            $phpWord  = WordFactory::load($filePath);
            $sections = $phpWord->getSections();
            $text     = '';
            foreach ($sections as $section) {
                $text .= $this->extractPhpWordElements($section->getElements());
            }
            return $text;
        } catch (\Throwable $e) {
            \Log::error('Word document extraction failed: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Recursively extract text from PhpWord elements (TextRun, Table, Text, Title, etc.)
     */
    private function extractPhpWordElements(array $elements): string
    {
        $text = '';
        foreach ($elements as $element) {
            if ($element instanceof \PhpOffice\PhpWord\Element\Text) {
                $text .= $element->getText() . ' ';
            } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                $text .= $this->extractPhpWordElements($element->getElements()) . "\n";
            } elseif ($element instanceof \PhpOffice\PhpWord\Element\Title) {
                $text .= $element->getText() . "\n";
            } elseif ($element instanceof \PhpOffice\PhpWord\Element\ListItem) {
                $text .= '• ' . $element->getText() . "\n";
            } elseif ($element instanceof \PhpOffice\PhpWord\Element\Table) {
                foreach ($element->getRows() as $row) {
                    foreach ($row->getCells() as $cell) {
                        $text .= $this->extractPhpWordElements($cell->getElements()) . "\t";
                    }
                    $text .= "\n";
                }
            } elseif (method_exists($element, 'getElements')) {
                $text .= $this->extractPhpWordElements($element->getElements()) . "\n";
            } elseif (method_exists($element, 'getText')) {
                $val = $element->getText();
                if (is_string($val)) {
                    $text .= $val . "\n";
                }
            }
        }
        return $text;
    }

    // Legacy API method kept for backwards compat
    public function index()
    {
        $bots = Bot::where('workspace_id', auth()->user()->workspace_id)->get();
        return response()->json(['success' => true, 'data' => $bots]);
    }

    /**
     * AI Playground — test the bot + RAG against uploaded docs & FAQ rules.
     * Returns the reply AND the diagnostic info (trigger, matched keywords,
     * retrieved chunks with similarity scores) so the user can validate training.
     */
    public function testAi(Request $request, \App\Services\RagService $ragService)
    {
        $bot      = $this->getBot();
        $question = $request->validate(['question' => 'required|string|max:1000'])['question'];

        $trigger         = 'ai_api';
        $matchedKeywords = null;
        $matchedChunks   = [];
        $context         = '';
        $enableAutoRules = ($bot->enable_auto_rules ?? true) !== false;
        $enableRag       = ($bot->enable_rag ?? true) !== false;

        // 1) Instant FAQ rule match (only if enable_auto_rules is true)
        $ruleMatch = null;
        if ($enableAutoRules) {
            $ruleMatch = $ragService->checkAutoRules($bot->workspace_id, $question);
        }

        if ($ruleMatch !== null) {
            $trigger         = 'auto_rule';
            $reply           = $ruleMatch['reply'];
            $matchedKeywords = is_array($ruleMatch['keywords'])
                ? implode(', ', array_slice($ruleMatch['keywords'], 0, 5))
                : (string) $ruleMatch['keywords'];
        } else {
            // 2) RAG retrieval with diagnostic scores (only if enable_rag is true)
            if ($enableRag) {
                $rag           = $ragService->retrieveRelevantChunks($bot->id, $question);
                $matchedChunks = $rag['chunks'];
                $context       = $rag['context'];
            }

            // 3) AI call (multi-turn history = none in single prompt)
            $aiService = new \App\Services\AiService($bot);
            $reply     = $aiService->generateReply($question, $context, []);

            if ($reply === $aiService->getFallbackReply()) {
                $trigger = 'fallback';
            }
        }

        // Persist to decision log so admin stats capture playground tests too
        try {
            \App\Models\AiDecisionLog::create([
                'conversation_id'  => null,
                'message_id'       => null,
                'trigger'          => $trigger,
                'matched_keywords' => $matchedKeywords,
                'context_sent'     => $context ? \Illuminate\Support\Str::limit($context, 2000) : null,
                'ai_provider'      => $bot->ai_provider ?: 'gemini',
                'model_type'       => $bot->model_type ?: 'default',
                'customer_message' => $question,
                'bot_reply'        => $reply,
                'response_time_ms' => 0,
            ]);
        } catch (\Throwable $e) {
            // non-fatal
        }

        $payload = [
            'success'          => true,
            'question'         => $question,
            'reply'            => $reply,
            'trigger'          => $trigger,
            'matched_keywords' => $matchedKeywords,
            'chunks'           => $matchedChunks,
            'context'          => $context,
        ];

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($payload);
        }

        return back()->with('playground', $payload);
    }
}
