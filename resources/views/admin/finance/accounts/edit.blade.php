@extends('layouts.admin')

@section('title', 'Modifier le compte')

@section('content')
<div class="admin-card max-w-[56rem]">
    <form action="{{ route('admin.comptes-paiement.update', $account) }}" method="POST">
        @method('PUT')
        @include('admin.finance.accounts._form')
    </form>
</div>
@endsection
