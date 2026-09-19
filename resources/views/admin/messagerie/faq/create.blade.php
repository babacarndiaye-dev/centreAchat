@extends('layouts.admin')

@section('title', 'Nouvelle question FAQ')

@section('content')
<div class="admin-card max-w-[56rem]">
    <form action="{{ route('admin.messagerie.faq.store') }}" method="POST">
        @csrf
        @include('admin.messagerie.faq._form')
    </form>
</div>
@endsection
