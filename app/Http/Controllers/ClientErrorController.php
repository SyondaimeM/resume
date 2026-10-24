<?php

namespace App\Http\Controllers;

use App\Exceptions\ClientScriptError;
use Illuminate\Http\Request;

class ClientErrorController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'kind' => 'required|in:script,promise',
            'source' => ['nullable', 'string', 'max:100', 'regex:/^[a-zA-Z0-9_.-]+\.js$/'],
            'line' => 'nullable|integer|min:0|max:1000000',
        ]);
        report(new ClientScriptError('Browser '.$data['kind'].' error in '.($data['source'] ?? 'unknown script').' at line '.($data['line'] ?? 0).'.'));

        return response()->noContent();
    }
}
