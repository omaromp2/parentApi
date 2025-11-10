<?php

use App\Jobs\ProcessDocumentJob;
use App\Models\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

it('validates file is required and correct type', function () {
    $this->postJson('/api/upload', [])->assertStatus(422);

    $this->postJson('/api/upload', [
        'file' => UploadedFile::fake()->create('bad.txt', 10, 'text/plain'),
    ])->assertStatus(422);
});

it('stores the file and queues processing', function () {
    Storage::fake('public');
    Queue::fake();

    $file = UploadedFile::fake()->create('stub.pdf', 100, 'application/pdf');

    $res = $this->postJson('/api/upload', ['file' => $file])
        ->assertCreated()
        ->json();

    expect($res)->toHaveKey('id');
    $doc = Document::find($res['id']);
    expect($doc)->not->toBeNull()
        ->and($doc->status)->toBe('uploaded');

    Storage::disk('public')->assertExists($doc->path);

    Queue::assertPushed(ProcessDocumentJob::class, function ($job) use ($doc) {
        return $job->docId === $doc->id;
    });
});
