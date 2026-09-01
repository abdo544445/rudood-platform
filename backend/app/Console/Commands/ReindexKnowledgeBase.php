<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\KnowledgeBase;

class ReindexKnowledgeBase extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'rag:reindex {--bot= : Reindex specific Bot ID}';

    /**
     * The console command description.
     */
    protected $description = 'Re-segment and compute vector embeddings for all KnowledgeBase documents and store in PostgreSQL vector database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Starting Knowledge Base Vector Embedding & Semantic Reindexing...');

        $query = KnowledgeBase::query();
        if ($botId = $this->option('bot')) {
            $query->where('bot_id', $botId);
        }

        $docs = $query->get();
        if ($docs->isEmpty()) {
            $this->warn('No knowledge base documents found to reindex.');
            return 0;
        }

        $totalChunks = 0;
        $bar = $this->output->createProgressBar($docs->count());
        $bar->start();

        foreach ($docs as $doc) {
            $chunks = $doc->syncChunksWithEmbeddings();
            $totalChunks += count($chunks);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Successfully vectorized and stored ({$totalChunks}) chunks for ({$docs->count()}) documents in the database.");

        return 0;
    }
}
