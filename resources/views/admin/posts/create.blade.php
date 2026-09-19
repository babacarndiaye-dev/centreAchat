@extends('layouts.admin')

@section('title', 'Nouvel article')

@section('content')
<div class="admin-card max-w-[64rem]">
    <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data">
        @include('admin.posts._form')
    </form>
</div>
@endsection
