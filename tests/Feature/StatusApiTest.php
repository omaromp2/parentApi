<?php

use App\Models\Document;

it('returns current status and summary', function () {
    $doc = Document::factory()->create([
        'status' => 'processing',
        'summary' => null,
    ]);

    $this->getJson("/api/status/{$doc->id}")
        ->assertOk()
        ->assertJson([
            'status' => 'processing',
            'summary' => null,
        ]);
});
