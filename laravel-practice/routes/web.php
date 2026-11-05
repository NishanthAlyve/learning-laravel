<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\GreetingController;
use App\Http\Controllers\NoteController;
use App\Models\Note;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/about', function() {
    return "Hey yo sup";
});

Route::get('/hello/{name}', function (string $name) {
    return "hello $name!";
});

Route::get("/greet", function (Request $request) {
    $name = $request->query('name', 'guest');
    $topic = $request->query('topic', 'gopal');

    return "Hello, {$name}! Your favourite topic is {$topic}";
});

Route::post("/greet", function (Request $request) {
    $name = $request->input('name');
    $topic = $request->input('topic');

    $data = [
    'message' => "Hello, {$name}! Your favourite topic is {$topic}"
    ];

    $jsonResponse = response()->json($data);
    return $jsonResponse;
});

Route::post("/greetValidated", function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:50',
        'topic' => 'required|string|max:100'
    ]);

    $name = $validated['name'];
    $topic = $validated['topic'];

    $data = [
    'message' => "Hello, {$name}! Your favourite topic is {$topic}"
    ];

    $jsonResponse = response()->json($data);
    return $jsonResponse;
});

Route::post("greetValidatedController", [GreetingController::class, 'greet']) -> name('greeting.submit');

Route::get("/page/{name}", function (string $name) {
    return view('greeting', [
        'name' => $name,
        'topics' => ['Routes', 'Controllers', 'Blade']
    ]);
})->name('greeting.form');

Route::get('/notes', function () {
    $notes = Note::all();

    return view('notes.index', ['notes' => $notes]);
})->name('notes.index');

Route::post('/notes', function (Request $request) {
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'body' => 'required|string',
    ]);

    $note = new Note();
    $note->title = $validated['title'];
    $note->body = $validated['body'];
    $note->save();

    return redirect()->route('notes.index');
})->name('notes.store');

Route::delete('/notes/{note}', function (Note $note) {
    $note->delete();
    return redirect()->route('notes.index');
})->name('notes.destroy');

Route::get('/notes/{note}/edit', function (Note $note) {
    return view('notes.edit', ['note' => $note]);
})->name('notes.edit');

Route::put('/notes/{note}', function (Request $request, Note $note) {
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'body' => 'required|string'
    ]);

    $note->title = $validated['title'];
    $note->body = $validated['body'];
    $note->save();

    return redirect()->route('notes.index');
})->name('notes.update');

Route::get('/notesController', [NoteController::class, 'index'])
    ->name('notesController/index');

Route::post('/notesController', [NoteController::class, 'store'])
    ->name('notes.controller.store');

Route::delete('/notesController/{note}', [NoteController::class, 'destroy'])
    ->name('notes.controller.destroy');

Route::get('/notesController/{note}/edit', [NoteController::class, 'edit'])
    ->name('notes.controller.edit');

Route::put('/notesController/{note}', [NoteController::class, 'update'])
    ->name('notes.controller.update');
