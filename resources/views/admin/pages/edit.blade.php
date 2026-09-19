@extends('layouts.admin')

@section('title', 'Modifier la page')

@section('content')
<div class="admin-card max-w-[64rem]">
    <form action="{{ route('admin.pages.update', $page) }}" method="POST">
        @method('PUT')
        @include('admin.pages._form')
    </form>
</div>
@endsection
