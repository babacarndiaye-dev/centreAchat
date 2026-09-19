@extends('layouts.admin')

@section('title', 'Modifier la catégorie')

@section('content')
<div class="admin-card max-w-[56rem]">
    <form action="{{ route('admin.categories.update', $category) }}" method="POST">
        @method('PUT')
        @include('admin.categories._form')
    </form>
</div>
@endsection
