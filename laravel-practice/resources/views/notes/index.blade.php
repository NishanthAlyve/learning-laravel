@extends('layouts.app')

@section('title', 'My Notes')

@section('content')
    <h2>My Notes</h2>

    @forelse($notes as $note)
        <article>
            <h3>{{ $note->title }}</h3>
            <p>{{ $note->body }}</p>
            <form action="{{ route('notes.controller.destroy', ['note' => $note->id]) }}" method="POST">
                @csrf
                @method('DELETE')

                <button type="submit">Delete note</button>
            </form>
            <a href="{{ route('notes.controller.edit', ['note' => $note->id]) }}">
                Edit Note
            </a>
        </article>
    @empty
        <p>No notes yet..</p>
    @endforelse


    <form action="{{ route('notes.controller.store') }}" method="POST">
        @csrf

        <div>
            <label for="title">Title</label>
            <input id="title" name="title" value="{{ old('title') }}" required>

            @error('title')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="body">Body</label>
            <textarea id="body" name="body" required>{{ old('body') }}</textarea>

            @error('body')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <button type="submit">Save note</button>
    </form>

@endsection
