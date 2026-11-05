# Laravel Learning: Revision Guide

This guide summarizes our lessons and the topics still to learn. Examples are
for revision, not instructions to register duplicate routes.

## Progress

- [x] PHP essentials used in our examples
- [x] Routes, route parameters, query parameters, and request bodies
- [x] Controllers and basic request injection
- [x] Validation and validation error responses
- [x] Text, JSON, and HTML responses
- [x] Blade views, forms, layouts, and components
- [x] CSRF protection and form method spoofing
- [x] Named routes, redirects, sessions, and flash messages
- [x] Migrations, models, and basic Eloquent operations
- [x] Notes CRUD using route functions and controller methods
- [ ] Resource routes: explained, not yet applied in the project
- [ ] Remaining topics listed at the end

## 1. PHP Essentials

### Variables, arrays, and functions

Use `$` when assigning or reading a variable. Array keys are usually strings:

```php
$user = [
    'name' => 'Sam',
    'topic' => 'APIs',
];

echo $user['name'];

function welcomeUser(array $user): string
{
    return "Welcome {$user['name']}!";
}

echo welcomeUser($user) . "\n";
```

- `=>` associates a key with a value.
- `array` and `string` are type declarations.
- Defining a function does not call it.
- `return` ends the function and gives a value to its caller.
- `echo` prints output; it does not automatically add a newline.
- `.` joins strings; `"\n"` adds a newline.

### Strings and braces

```php
echo "Hello $name";       // Substitutes the variable
echo "Hello {$name}";     // Same result
echo "{$name}_profile";   // Separates the variable from adjoining text
echo 'Hello $name';       // Prints literal $name
echo "Hello {$user['name']}";
```

Braces inside strings clarify variable interpolation. They do not execute
arbitrary function calls.

```php
print_r(array_keys($user));   // Inspect an array of keys
print_r(array_values($user)); // Inspect an array of values
echo implode(', ', array_keys($user)); // Join keys into printable text
```

Do not interpolate a whole array into a string: that causes an
`Array to string conversion` warning.

### Loops and objects

```php
foreach ($user as $key => $value) {
    echo "$key => $value\n";
}

$note->title;             // Access a model attribute
$note->save();            // Call an object method
App\Models\Note::find(1); // Call an Eloquent method through the model class
```

- A class is a blueprint for objects; `new` creates an object.
- A method is a function belonging to a class.
- `public` allows a method to be called from outside the class.
- `namespace` groups classes; `use` imports a class name.
- `extends` means a class inherits from another class.

These class concepts have been introduced; deeper PHP OOP is still ahead.

## 2. How a Laravel Request Works

```text
Browser/API client
    -> route
    -> route function or controller method
    -> optional validation and database work
    -> response
```

Laravel takes the value returned by your handler and sends an HTTP response.
Use `return`, rather than relying on `echo`, inside a route/controller.

### Important project files

| Location | Purpose |
|---|---|
| [routes/web.php](routes/web.php) | Browser-facing routes |
| [app/Http/Controllers](app/Http/Controllers) | Request-handling classes |
| [app/Models/Note.php](app/Models/Note.php) | Model for note records |
| [database/migrations](database/migrations) | Database structure changes |
| [resources/views](resources/views) | Blade templates |
| [resources/views/layouts/app.blade.php](resources/views/layouts/app.blade.php) | Shared page structure |
| [resources/views/notes/index.blade.php](resources/views/notes/index.blade.php) | Notes list and create form |
| [resources/views/notes/edit.blade.php](resources/views/notes/edit.blade.php) | Edit form |

## 3. Routes and Incoming Data

### Route parameters

```php
Route::get('/hello/{name}', function (string $name) {
    return "Hello, {$name}!";
});
```

Visiting `/hello/Sam` passes `Sam` into `$name`. End the route declaration with
`});`: close the function, close the call, and terminate the statement.

### Query parameters

For `/greet?name=Sam&topic=APIs`:

```php
use Illuminate\Http\Request;

Route::get('/greet', function (Request $request) {
    $name = $request->query('name', 'guest');

    return "Hello, {$name}!";
});
```

- `?` starts the query string; `&` separates parameters.
- `query('name', 'guest')` reads the query field, with a default if it is absent.
- Laravel supplies the incoming Request object because of `Request $request`.

### Request bodies

```php
$name = $request->input('name');
```

Read this as: "Call the request object's input method to read the field name."
`input()` reads form/JSON input and can also read query parameters.
`query()` reads only query parameters.

For JSON requests, send:

```text
Content-Type: application/json
Accept: application/json
```

The first describes the request body. The second requests a JSON response,
including JSON validation errors.

## 4. Responses

```php
return 'Hello!'; // Text

return response()->json([
    'message' => 'Hello!',
]); // JSON response

return view('notes.index', [
    'notes' => $notes,
]); // Rendered HTML
```

`response()` provides a response factory; `->json()` creates a JSON response
with the appropriate content type.

`view('notes.index', ...)` selects `resources/views/notes/index.blade.php`.
Dots separate folders; omit `.blade.php`. The data array's key `notes`
becomes `$notes` inside the view. It is independent of the folder name.

Common statuses covered:

| Status | Meaning |
|---|---|
| 200 | Successful response |
| 201 | Resource created, e.g. `response()->json($note, 201)` |
| 404 | Requested record/page not found |
| 419 | Common Laravel response for a failed CSRF/session check |
| 422 | JSON validation failure |
| 500 | Server error, such as an undefined variable |

## 5. Validation

These two forms are equivalent:

```php
$validated = $request->validate([
    'title' => 'required|string|max:255',
    'body' => 'required|string',
]);
```

```php
$validated = $request->validate([
    'title' => ['required', 'string', 'max:255'],
    'body' => ['required', 'string'],
]);
```

| Rule | Meaning |
|---|---|
| `required` | Present and not empty |
| `string` | Must be a string |
| `integer` | Integer or an accepted integer representation |
| `email` | Valid email format |
| `min:3` | For strings, at least three characters |
| `max:255` | For strings, at most 255 characters |
| `nullable` | Allows null; non-implicit rules skip null values |
| `sometimes` | Validate only if the field was submitted |
| `in:draft,published` | One of the listed values |

The meaning of size rules depends on the data type; numbers are checked by
value, while strings are checked by length.

Custom messages:

```php
$validated = $request->validate([
    'title' => ['required', 'string', 'max:255'],
], [
    'title.required' => 'Please give your note a title.',
]);
```

On success, `validate()` returns the validated data array.
On failure, it stops normal handler execution:

- Normal browser forms: redirect back with errors and previous input.
- Requests expecting JSON: return a 422 response with errors.

Validation works with POST, PUT, and PATCH. You choose the rules; the HTTP
method does not automatically change them. Browser `required` attributes
are helpful UX, not a replacement for server validation.

## 6. Blade and Forms

### Display data, conditions, and loops

```blade
{{ $note->title }}

@if(count($topics) > 0)
    <p>You have topics.</p>
@else
    <p>No topics yet.</p>
@endif

@foreach($topics as $topic)
    <p>{{ $topic }}</p>
@endforeach

@forelse($notes as $note)
    <h3>{{ $note->title }}</h3>
@empty
    <p>No notes yet.</p>
@endforelse
```

`{{ ... }}` escapes output so text is not interpreted as HTML.
Do not use `$note` outside the loop to build a note-specific button:
with an empty list, it may never have been assigned.

### Form fields, errors, and old input

```blade
<form action="{{ route('notes.controller.store') }}" method="POST">
    @csrf

    <input name="title" value="{{ old('title') }}" required>

    @error('title')
        <p>{{ $message }}</p>
    @enderror

    <textarea name="body" required>{{ old('body') }}</textarea>

    <button type="submit">Save note</button>
</form>
```

- `action` selects the destination; `method` selects the HTTP method.
- Input `name` attributes become request field names.
- A textarea's initial text goes between its tags, not in `value`.
- `old()` retrieves input from the previous failed submission.
- `@error` displays a field's validation error.
- For editing, `old('title', $note->title)` falls back to the saved value.

### CSRF and method spoofing

```blade
<form action="{{ route('notes.controller.update', ['note' => $note->id]) }}"
      method="POST">
    @csrf
    @method('PUT')
</form>
```

HTML forms support GET and POST, not PUT or DELETE.
`@method('PUT')` generates a hidden `_method=PUT` field. The browser sends
POST, but Laravel treats it as PUT when selecting a route.
Use `@method('DELETE')` for deletion.

`@csrf` generates a hidden CSRF token. The browser also sends the session
cookie. This protects browser-facing state-changing routes.
An API client can send real PUT/DELETE requests without method spoofing,
but web routes still require valid CSRF/session information.
Do not disable CSRF protection just to test a web endpoint.

### Layouts

Shared layout:

```blade
<title>@yield('title', 'Laravel Practice')</title>
<main>@yield('content')</main>
```

Child view:

```blade
@extends('layouts.app')

@section('title', 'My notes')

@section('content')
    <h2>My notes</h2>
@endsection
```

`@yield` defines a slot in the layout; `@section` supplies its contents.

### Components

A file named `resources/views/components/alert.blade.php`:

```blade
<div role="status">{{ $slot }}</div>
```

Can be used as:

```blade
<x-alert>Your note was saved.</x-alert>
```

`$slot` receives content between the component tags.
A layout is the page skeleton; a component is a reusable piece.

## 7. Named Routes, Redirects, and Flash Messages

```php
Route::get('/page/{name}', function (string $name) {
    return view('greeting', ['name' => $name]);
})->name('greeting.form');
```

```php
return redirect()
    ->route('greeting.form', ['name' => $name])
    ->with('success', 'Greeting submitted!');
```

```blade
@if(session('success'))
    <x-alert>{{ session('success') }}</x-alert>
@endif
```

- Route names are labels, not URLs. Dot notation is conventional.
- `route()` generates a URL; its parameter array fills placeholders.
- A flash message is session data available for the next request.
- POST -> redirect -> GET avoids resubmitting the POST when refreshing.

Our learning project retains both `/notes` route-function examples and
`/notesController` controller examples. They share views, so a form's named
route determines which implementation handles it, regardless of which
listing page displayed the form.

## 8. Migrations and Models

**Migration = table structure. Model = working with records.**

```php
Schema::create('notes', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('body');
    $table->timestamps();
});
```

- `id()` creates an auto-incrementing primary key.
- `string()` defines short text; its default length is 255.
- `text()` holds longer text.
- `timestamps()` adds `created_at` and `updated_at`.
- `up()` applies a migration; `down()` reverses it.
- Generating a migration does not create the table: running it does.
- Laravel records applied migrations so they do not run again.

The `Note` model conventionally uses the `notes` table, primary key `id`,
and timestamps. An almost-empty model is normal; it does not define columns.

For future schema changes after a migration is applied, normally create a
new migration. Do not assume editing an old migration will update the table.

## 9. Eloquent CRUD

### Create

```php
$note = new App\Models\Note();
$note->title = 'First note';
$note->body = 'Learning Laravel';
$note->save();

$note->id;
```

After a successful first save, Eloquent updates the same object with the
generated ID and timestamps. `save()` returns success, not the generated ID.

### Read

```php
App\Models\Note::find($note->id); // One record, or null if absent
App\Models\Note::all();          // Collection of all records
```

A collection is a list-like object you can loop over.
Use pagination for larger datasets; that is still to learn.

### Update

```php
$note->title = $validated['title'];
$note->body = $validated['body'];
$note->save();
```

An existing model updates its row rather than inserting a new one.
Check assignment keys: assigning `title` to both attributes overwrites the
body in the database. Fixing code does not restore previously overwritten data.

### Delete and route model binding

```php
Route::delete('/notes/{note}', function (App\Models\Note $note) {
    $note->delete();

    return redirect()->route('notes.index');
});
```

Matching `{note}` with `Note $note` lets Laravel fetch the record using the
URL ID. A missing record produces 404. `delete()` permanently removes the
row in our current model.

Our practice notes do not yet have ownership checks. Authentication and
authorization must be added before using this as a multi-user application.

## 10. Controllers and Resource Routes

Controllers group related request-handling methods:

| Method | Purpose |
|---|---|
| `index` | Display a list |
| `create` | Display a creation form |
| `store` | Save a new record |
| `show` | Display one record |
| `edit` | Display an editing form |
| `update` | Save changes to an existing record |
| `destroy` | Delete a record |

Our create form is on the listing page, so we have not needed a separate
`create()` method. We also have not added a separate `show()` page.

```php
Route::get('/notesController', [NoteController::class, 'index']);
```

This tells Laravel which class and method to call. Methods declared directly
in a class need names; route closures are anonymous functions.

### Resource routes: explained, still to apply

Replace the five explicit controller routes with the following only when
ready. Do not register both sets at the same time:

```php
Route::resource('notesController', NoteController::class)
    ->parameters(['notesController' => 'note'])
    ->only(['index', 'store', 'edit', 'update', 'destroy'])
    ->names([
        'index' => 'notesController/index',
        'store' => 'notes.controller.store',
        'edit' => 'notes.controller.edit',
        'update' => 'notes.controller.update',
        'destroy' => 'notes.controller.destroy',
    ]);
```

- `only()` selects implemented actions.
- `parameters()` keeps `{note}` compatible with model binding.
- `names()` preserves the names our existing views and redirects use.
- Resource update routes accept both PUT and PATCH.

With a conventional resource name and matching views, a fresh app could use
`Route::resource('notes', NoteController::class)` with much less configuration.

## 11. Commands and Testing Reminders

Run Artisan commands from the Laravel project folder:

```bash
php artisan serve
php artisan make:controller NoteController
php artisan make:model Note -m
php artisan migrate
php artisan tinker
```

- Keep the server terminal open; use a second terminal for other commands.
- Tinker runs PHP with the application loaded. Type `exit` to leave.
- Avoid destructive database reset commands on data you want to keep.
- In curl, `-d` sends a body and defaults to POST; `-X PUT` selects real PUT.
- `-i` displays response headers and status.
- Interactive zsh may reject pasted `#` comment lines; omit them.
- A shell continuation backslash must be the final character on its line.
- A terminal `%` after output can indicate a missing final newline.
- Do not share session cookies or authentication tokens.

## 12. Topics Still to Learn

This is our remaining roadmap, not an exhaustive list of every Laravel feature.

### Next: Organization and Eloquent

- [ ] Apply resource routes and inspect the registered routes.
- [ ] Form Request classes: authorization, reusable validation, custom messages.
- [ ] Querying: `where`, ordering, search, `first`, and `findOrFail`.
- [ ] Pagination and preserving search/filter query parameters.
- [ ] Mass assignment: `create`, `update`, and `$fillable`.
- [ ] Model casts, accessors, and mutators.
- [ ] Factories and seeders for test/sample data.
- [ ] Schema changes, foreign keys, and indexes.
- [ ] Relationships: one-to-many and many-to-many.
- [ ] Eager loading and avoiding N+1 queries.
- [ ] Transactions and soft deletes.

### PHP and Application Structure

- [ ] Deeper OOP: visibility, inheritance, interfaces, and traits.
- [ ] Dependency injection and Laravel's service container.
- [ ] Middleware and the request lifecycle.
- [ ] Configuration, environment variables, and configuration caching.
- [ ] Service providers and organizing shared application logic.

### Authentication and Authorization

- [ ] Registration, login, logout, sessions, and password hashing.
- [ ] Authentication middleware and protecting routes.
- [ ] Relate notes to their owners.
- [ ] Gates and policies: enforce who can view/change/delete a note.
- [ ] Password resets and email verification.

### REST APIs

- [ ] Separate API routes from browser routes.
- [ ] POST vs PUT vs PATCH semantics and partial-update validation.
- [ ] Consistent JSON with API Resources and pagination.
- [ ] API authentication using Sanctum.
- [ ] Rate limiting, CORS, and consistent error responses.
- [ ] API client testing without browser-form method spoofing.

### Testing and Debugging

- [ ] Feature and unit tests.
- [ ] Database isolation, factories, and assertions.
- [ ] Test successful CRUD and invalid submissions.
- [ ] Test missing records, empty lists, and authorization failures.
- [ ] Logs, exceptions, and debugging tools.

### Practical Features and Production

- [ ] File uploads, validation, and storage.
- [ ] Email and notifications.
- [ ] Events and listeners.
- [ ] Jobs, queues, retries, and failed jobs.
- [ ] Scheduled tasks.
- [ ] Caching and invalidation.
- [ ] Deployment, production configuration, and disabling debug output.
- [ ] Backups, monitoring, and safe production migrations.

## Revision Exercises

1. Explain the difference between a route name and its URL.
2. Explain `query('name')` vs `input('name')`.
3. Submit an invalid form: check errors and retained values.
4. Explain why POST plus `_method=PUT` matches a PUT route.
5. Create a note, inspect its generated ID, edit it, then delete it.
6. Delete all practice notes: the empty list should still render correctly.
7. Explain why migrations define columns but models do not.
8. Trace form -> route -> controller -> database -> redirect -> view.
9. Explain why a missing bound model gives 404.
10. Compare the original route functions with the controller methods.
