@extends('layouts.admin')

@section('title', 'Modifier le produit')

@section('content')
<div class="admin-card max-w-[56rem]">
    <form action="{{ route('admin.produits.update', $product) }}" method="POST" enctype="multipart/form-data">
        @method('PUT')
        @include('admin.products._form')
    </form>

    {{-- Kept outside the main form: a <form> can't be nested inside another <form> in HTML. --}}
    @foreach($product->images as $image)
        <form id="delete-image-{{ $image->id }}" action="{{ route('admin.produits.images.destroy', $image) }}" method="POST" class="hidden">
            @csrf @method('DELETE')
        </form>
    @endforeach
</div>
@endsection
