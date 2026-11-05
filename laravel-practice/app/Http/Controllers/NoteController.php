<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    function index() {
        $notes = Note::all();
        return view('notes.index', ['notes' => $notes]);
    }

    function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string'
        ]);

        $note = new Note();
        $note->title = $validated['title'];
        $note->body = $validated['body'];
        $note->save();

        return redirect()->route('notesController/index');
    }

    function destroy(Note $note) {
        $note->delete();
        return redirect()->route('notesController/index');
    }

    function edit(Note $note) {
        return view('notes.edit', ['note' => $note]);
    }

    function update(Request $request, Note $note) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string'
        ]);

        $note->title = $validated['title'];
        $note->body = $validated['body'];
        $note->save();

        return redirect()->route('notesController/index');
    }
}
