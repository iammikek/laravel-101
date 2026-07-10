@extends('layouts.app')

@section('title', $pageTitle ?? 'Add item')

@section('content')
<section class="panel narrow">
  <h1>{{ $pageTitle ?? 'Add item' }}</h1>
  <p class="muted">Session login required. Submits a normal HTML form with CSRF protection.</p>

  <form method="post" action="{{ route('shop.items.create') }}">
    @csrf
    <div class="mb-3">
      <label for="name">Name</label>
      <input type="text" id="name" name="name" value="{{ old('name') }}" required>
      @error('name')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div class="mb-3">
      <label for="description">Description</label>
      <textarea id="description" name="description">{{ old('description') }}</textarea>
    </div>
    <div class="mb-3">
      <label for="price">Price</label>
      <input type="number" step="0.01" id="price" name="price" value="{{ old('price') }}" required>
      @error('price')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div class="mb-3">
      <label for="category_id">Category</label>
      <select id="category_id" name="category_id">
        <option value="">None</option>
        @foreach ($categories as $category)
          <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="actions">
      <button type="submit" class="button">Save item</button>
      <a class="button secondary" href="{{ route('shop.items.index') }}">Cancel</a>
    </div>
  </form>
</section>
@endsection
