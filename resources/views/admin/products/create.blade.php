@extends('layouts.admin')

@section('title', 'Nouveau produit')

@section('content')
<div class="admin-card max-w-[56rem]">
    <form action="{{ route('admin.produits.store') }}" method="POST" enctype="multipart/form-data">
        @include('admin.products._form')
    </form>
</div>
@endsection
