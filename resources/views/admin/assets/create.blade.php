@extends('layouts.admin')

@section('title', 'Nouvelle immobilisation')

@section('content')
<div class="admin-card max-w-[42rem]">
    <form action="{{ route('admin.immobilisations.store') }}" method="POST">
        @include('admin.assets._form')
    </form>
</div>
@endsection
