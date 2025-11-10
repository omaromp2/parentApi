<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Document;
use App\Services\DocumentAiService;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProcessDocumentJob implements ShouldQueue
{
    use Queueable;
    use Dispatchable;
    use InteractsWithQueue;
    use SerializesModels;

    public function __construct(public int $docId)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(DocumentAiService $docAi): void
    {
        //
        $doc = Document::findOrFail($this->docId);
        $doc->update(['status' => 'processing']);

        $abs = Storage::disk('public')->path($doc->path);
        $mime = match (strtolower(pathinfo($abs, PATHINFO_EXTENSION))) {
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            default => 'application/octet-stream',
        };

        $result = $docAi->process($abs, $mime);

        $doc->update([
            'status'    => 'done',
            'extracted' => ['entities' => $result['entities']],
            'summary'   => $result['summary'],
        ]);
    }

    public function failed(Throwable $e): void
    {
        Document::where('id', $this->docId)->update([
            'status' => 'failed',
            'error'  => $e->getMessage(),
        ]);
    }

}
