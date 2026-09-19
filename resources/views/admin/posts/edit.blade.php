@extends('layouts.admin')

@section('title', "Modifier l'article")

@section('content')
<div class="admin-card max-w-[64rem]">
    <form action="{{ route('admin.articles.update', $post) }}" method="POST" enctype="multipart/form-data">
        @method('PUT')
        @include('admin.posts._form')
    </form>
</div>
@endsection
