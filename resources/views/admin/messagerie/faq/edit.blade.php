@extends('layouts.admin')

@section('title', 'Modifier la question')

@section('content')
<div class="admin-card max-w-[56rem]">
    <form action="{{ route('admin.messagerie.faq.update', $entry) }}" method="POST">
        @csrf
        @method('PATCH')
        @include('admin.messagerie.faq._form')
    </form>
</div>
@endsection
