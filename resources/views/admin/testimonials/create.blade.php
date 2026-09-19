@extends('layouts.admin')

@section('title', 'Nouvel avis')

@section('content')
<div class="admin-card max-w-[48rem]">
    <form action="{{ route('admin.avis.store') }}" method="POST">
        @include('admin.testimonials._form')
    </form>
</div>
@endsection
