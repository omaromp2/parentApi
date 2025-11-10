<?php

use App\Jobs\ProcessDocumentJob;
use App\Models\Document;
use App\Services\DocumentAiService;
use Illuminate\Support\Facades\Storage;

it('updates document with extracted data and summary', function () {
    // Arrange: a stored file and a document record
    Storage::fake('public');
    $path = 'paystubs/demo.pdf';
    Storage::disk('public')->put($path, 'fake-pdf-bytes');

    $doc = Document::factory()->create([
        'path' => $path,
        'status' => 'uploaded',
    ]);

    // Bind a fake service into the container
    app()->bind(DocumentAiService::class, function () {
        return new class () extends DocumentAiService {
            public function process(string $absolutePath, string $mime): array
            {
                return [
                    'entities' => [
                        ['type' => 'employee_name', 'text' => 'Jane Dev'],
                        ['type' => 'net_pay', 'text' => '$2,345.67'],
                    ],
                    'summary' => [
                        'headline' => 'Paystub summary',
                        'fields' => [
                            'employee_name' => 'Jane Dev',
                            'net_pay' => '$2,345.67',
                        ],
                    ],
                ];
            }
        };
    });

    // Act
    (new ProcessDocumentJob($doc->id))->handle(app(DocumentAiService::class));


    // Assert
    $doc->refresh();
    expect($doc->status)->toBe('done')
        ->and($doc->summary['fields']['employee_name'])->toBe('Jane Dev')
        ->and($doc->summary['fields']['net_pay'])->toBe('$2,345.67')
        ->and($doc->extracted['entities'])->toHaveCount(2);
});
