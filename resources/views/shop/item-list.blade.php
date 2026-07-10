@extends('layouts.app')

@section('title', 'Items')

@section('content')
<div class="page-header">
  <div>
    <h1>Items</h1>
    <p class="muted">{{ $totalCount }} total · page {{ $page }} of {{ $totalPages }}</p>
  </div>
  @auth
    <a class="button" href="{{ route('shop.items.create') }}">Add item</a>
  @endauth
</div>

<form method="get" class="filter-form panel">
  <h2>Filter</h2>
  <div class="field-grid">
    <div class="mb-3">
      <label for="name_contains">Name contains</label>
      <input type="text" id="name_contains" name="name_contains" value="{{ $filters['name_contains'] ?? '' }}">
    </div>
    <div class="mb-3">
      <label for="category_id">Category</label>
      <select id="category_id" name="category_id">
        <option value="">Any</option>
        @foreach ($categories as $category)
          <option value="{{ $category->id }}" @selected(($filters['category_id'] ?? '') == $category->id)>{{ $category->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="mb-3">
      <label for="min_price">Min price</label>
      <input type="number" step="0.01" id="min_price" name="min_price" value="{{ $filters['min_price'] ?? '' }}">
    </div>
    <div class="mb-3">
      <label for="max_price">Max price</label>
      <input type="number" step="0.01" id="max_price" name="max_price" value="{{ $filters['max_price'] ?? '' }}">
    </div>
  </div>
  <div class="actions">
    <button type="submit" class="button">Apply</button>
    <a class="button secondary" href="{{ route('shop.items.index') }}">Clear</a>
  </div>
</form>

<section class="panel">
  @if ($items->count() > 0)
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Category</th>
          <th>Price</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($items as $item)
          <tr>
            <td><a href="{{ route('shop.items.show', $item->id) }}">{{ $item->name }}</a></td>
            <td>{{ $item->category?->name ?? '—' }}</td>
            <td>${{ $item->price }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>

    @if ($totalPages > 1)
      <nav class="pagination">
        @if ($page > 1)
          <a href="{{ route('shop.items.index', array_merge($filters, ['page' => $page - 1])) }}">← Previous</a>
        @endif
        @if ($page < $totalPages)
          <a href="{{ route('shop.items.index', array_merge($filters, ['page' => $page + 1])) }}">Next →</a>
        @endif
      </nav>
    @endif
  @else
    <p>No items match your filters.</p>
  @endif
</section>
@endsection
