@extends('layouts.admin')

@section('title', "Modifier l'avis")

@section('content')
<div class="admin-card max-w-[48rem]">
    <form action="{{ route('admin.avis.update', $testimonial) }}" method="POST">
        @method('PUT')
        @include('admin.testimonials._form')
    </form>
</div>
@endsection
