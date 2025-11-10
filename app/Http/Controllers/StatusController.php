<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    //
    public function show(int $id)
    {
        $doc = Document::findOrFail($id);

        return response()->json([
            'status'  => $doc->status,
            'summary' => $doc->summary,
            'error'   => $doc->error,
        ]);
    }
}
