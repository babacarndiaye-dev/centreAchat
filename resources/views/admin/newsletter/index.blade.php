@extends('layouts.admin')

@section('title', 'Abonnés newsletter')

@section('content')
<p class="text-sm text-terroir-dark/50">{{ $subscribers->total() }} abonné(s)</p>

<div class="admin-card mt-6 overflow-x-auto p-0">
    <table class="admin-table">
        <thead>
            <tr>
                <th class="pl-6">E-mail</th>
                <th class="pr-6">Inscrit le</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subscribers as $subscriber)
                <tr>
                    <td class="pl-6 font-semibold text-terroir-dark">{{ $subscriber->email }}</td>
                    <td class="pr-6 text-terroir-dark/60">{{ optional($subscriber->subscribed_at)->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="2" class="py-8 text-center text-terroir-dark/40">Aucun abonné.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $subscribers->links() }}</div>
@endsection
