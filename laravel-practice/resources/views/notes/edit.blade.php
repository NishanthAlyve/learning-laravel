@extends('layouts.app')

@section('title', 'Edit note')

@section('content')
    <h2>Edit note</h2>

    <form action="{{ route('notes.controller.update', ['note' => $note->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div>
            <label for="title">Title</label>
            <input id="title" name="title"
                   value="{{ old('title', $note->title) }}" required>

            @error('title')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="body">Body</label>
            <textarea id="body" name="body" required>{{ old('body', $note->body) }}</textarea>

            @error('body')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <button type="submit">Save changes</button>
    </form>
@endsection
