@extends('layouts.admin')

@section('title', 'Nouveau compte')

@section('content')
<div class="admin-card max-w-[56rem]">
    <form action="{{ route('admin.comptes-paiement.store') }}" method="POST">
        @include('admin.finance.accounts._form')
    </form>
</div>
@endsection
