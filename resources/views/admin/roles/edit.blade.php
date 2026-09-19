@extends('layouts.admin')

@section('title', 'Modifier le rôle')

@section('content')
<form action="{{ route('admin.roles.update', $role) }}" method="POST">
    @csrf
    @method('PATCH')
    @include('admin.roles._form')
</form>
@endsection
