@extends('layouts.admin')

@section('title', 'Nouvelle page')

@section('content')
<div class="admin-card max-w-[64rem]">
    <form action="{{ route('admin.pages.store') }}" method="POST">
        @include('admin.pages._form')
    </form>
</div>
@endsection
