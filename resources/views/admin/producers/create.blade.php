@extends('layouts.admin')

@section('title', 'Nouveau producteur')

@section('content')
<div class="admin-card max-w-[56rem]">
    <form action="{{ route('admin.producteurs.store') }}" method="POST" enctype="multipart/form-data">
        @include('admin.producers._form')
    </form>
</div>
@endsection
