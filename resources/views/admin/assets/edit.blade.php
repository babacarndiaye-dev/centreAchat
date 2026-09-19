@extends('layouts.admin')

@section('title', "Modifier l'immobilisation")

@section('content')
<div class="admin-card max-w-[42rem]">
    <form action="{{ route('admin.immobilisations.update', $asset) }}" method="POST">
        @method('PUT')
        @include('admin.assets._form')
    </form>
</div>
@endsection
