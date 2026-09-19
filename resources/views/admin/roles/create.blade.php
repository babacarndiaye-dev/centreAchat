@extends('layouts.admin')

@section('title', 'Nouveau rôle')

@section('content')
<form action="{{ route('admin.roles.store') }}" method="POST">
    @csrf
    @include('admin.roles._form')
</form>
@endsection
