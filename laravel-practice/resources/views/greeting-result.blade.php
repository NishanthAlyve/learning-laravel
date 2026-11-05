@extends('layouts.app')

@section('title', 'Your greeting')

@section('content')
    <h2>Hello, {{ $name }}!</h2>
    <p>Your favourite topic is {{ $topic }}.</p>
@endsection
