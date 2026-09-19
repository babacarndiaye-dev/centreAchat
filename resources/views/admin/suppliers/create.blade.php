@extends('layouts.admin')

@section('title', 'Nouveau fournisseur')

@section('content')
<div class="admin-card max-w-[48rem]">
    <form action="{{ route('admin.fournisseurs.store') }}" method="POST">
        @include('admin.suppliers._form')
    </form>
</div>
@endsection
