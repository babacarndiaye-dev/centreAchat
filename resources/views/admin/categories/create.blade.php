@extends('layouts.admin')

@section('title', 'Nouvelle catégorie')

@section('content')
<div class="admin-card max-w-[56rem]">
    <form action="{{ route('admin.categories.store') }}" method="POST">
        @include('admin.categories._form')
    </form>
</div>
@endsection
