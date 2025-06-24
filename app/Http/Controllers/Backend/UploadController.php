<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    /**
     * Handle image upload from the rich text editor.
     */
    public function store(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|max:10240',
        ]);

        $path = $request->file('upload')->store('uploads', 'public');

        return response()->json(['url' => Storage::disk('public')->url($path)]);
    }
}
