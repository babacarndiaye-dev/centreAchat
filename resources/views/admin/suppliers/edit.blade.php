@extends('layouts.admin')

@section('title', 'Modifier le fournisseur')

@section('content')
<div class="admin-card max-w-[48rem]">
    <form action="{{ route('admin.fournisseurs.update', $supplier) }}" method="POST">
        @method('PUT')
        @include('admin.suppliers._form')
    </form>
</div>
@endsection
