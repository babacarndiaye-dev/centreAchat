@extends('layouts.admin')

@section('title', 'Modifier le producteur')

@section('content')
<div class="admin-card max-w-[56rem]">
    <form action="{{ route('admin.producteurs.update', $producer) }}" method="POST" enctype="multipart/form-data">
        @method('PUT')
        @include('admin.producers._form')
    </form>
</div>
@endsection
