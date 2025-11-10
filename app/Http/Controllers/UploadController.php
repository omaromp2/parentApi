<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessDocumentJob;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\File;

class UploadController extends Controller
{
    // upload controller
    public function store(Request $request)
    {
        // TODO move to separate FormRequest class
        $request->validate([
            'file' => ['required', File::types(['pdf','png','jpg','jpeg'])->max(8 * 1024)],
        ]);

        $storedPath = $request->file('file')->store('paystubs', 'public');

        $doc = Document::create([
            'original_name' => $request->file('file')->getClientOriginalName(),
            'path'          => $storedPath,
            'status'        => 'uploaded',
        ]);

        ProcessDocumentJob::dispatch($doc->id);

        return response()->json(['id' => $doc->id], 201);
    }
}
