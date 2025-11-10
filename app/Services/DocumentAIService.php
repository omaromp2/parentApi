<?php

namespace App\Services;

use Google\Cloud\DocumentAI\V1\Client\DocumentProcessorServiceClient as ClientDocumentProcessorServiceClient;
use Google\Cloud\DocumentAI\V1\ProcessRequest;
use Google\Cloud\DocumentAI\V1\RawDocument;

class DocumentAiService
{
    public function process(string $absolutePath, string $mime): array
    {
        $client = new ClientDocumentProcessorServiceClient([
            'credentials' => env('GOOGLE_APPLICATION_CREDENTIALS'),
        ]);

        $name = $client->processorName(
            env('DOC_AI_PROJECT'),
            env('DOC_AI_LOCATION', 'us'),
            env('DOC_AI_PROCESSOR'),
        );

        $raw = (new RawDocument())
            ->setContent(file_get_contents($absolutePath))
            ->setMimeType($mime);

        $request = (new ProcessRequest())
            ->setName($name)
            ->setRawDocument($raw);

        $doc = $client->processDocument($request)->getDocument();

        // Simplified extraction into a generic array:
        $entities = [];
        foreach ($doc->getEntities() as $e) {
            $entities[] = [
                'type' => $e->getType(),
                'text' => $e->getMentionText(),
            ];
        }

        // Dumb summary mapping for demo:
        $summary = [
            'headline' => 'Paystub summary',
            'fields' => collect($entities)->mapWithKeys(fn ($e) => [$e['type'] => $e['text']])->all(),
        ];

        return ['entities' => $entities, 'summary' => $summary];
    }
}
