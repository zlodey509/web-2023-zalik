<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use Illuminate\Support\Facades\Storage;

class UploadFileController extends Controller
{
    public function storeFile(Request $request) {
        $validated = $request->validate([
                'file' => 'required|file|mimes:pdf',
            ]);
        $file_name = $validated['file']->getClientOriginalName();
        Storage::put($file_name, $validated['file']);
        $file = File::create(['filename' => $validated['file']->getClientOriginalName(), 'file_size' => $validated['file']->getSize()]);
        return response()->json(['data' => $file], 201);
    }
}
