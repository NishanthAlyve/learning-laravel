@extends('layouts.app')

@section('title', 'Greeting')

@section('content')
    <h1>Hello, {{ $name }}!</h1>
    <p>You are learning Laravel.</p>

    <p>Topics Learnt</p>
    @if(count($topics) > 0)
        <ul>
            @foreach($topics as $topic)
                <li>{{ $topic }}</li>
            @endforeach
        </ul>
    @else
        <p>No Topics Yet.</p>
    @endif

    @if(session('success'))
        <x-alert>
            {{ session('success') }}
        </x-alert>
    @endif

    <form action="{{ route('greeting.submit') }}" method="POST">
    @csrf

    <label>
        Name:
        <input type="text" name="name" required>
    </label>

    @error('name')
        <p>{{ $message }}</p>
    @enderror

    <label>
        Topic:
        <input type="text" name="topic" required>
    </label>

    @error('topic')
        <p>{{ $message }}</p>
    @enderror

    <button type="submit">Greet me</button>
</form>
@endsection
