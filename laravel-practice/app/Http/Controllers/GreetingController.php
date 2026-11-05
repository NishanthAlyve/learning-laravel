<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GreetingController extends Controller
{
    function greet(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'topic' => 'required|string|max:100'
        ]);

        $name = $validated['name'];
        $topic = $validated['topic'];

        // $data = [
        // 'message' => "Hello, {$name}! Your favourite topic is {$topic}"
        // ];

        // $jsonResponse = response()->json($data);
        // return view('greeting-result', [
        //     'name' => $name,
        //     'topic' => $topic,
        // ]);

        return redirect()
            ->route('greeting.form', ['name' => $name])
            ->with('success', "hello, {$name}! Your favourite topic is {$topic}.");
    }
}
