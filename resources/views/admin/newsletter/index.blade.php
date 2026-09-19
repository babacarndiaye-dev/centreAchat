@extends('layouts.admin')

@section('title', 'Abonnés newsletter')

@section('content')
<p class="uk-text-small uk-text-muted">{{ $subscribers->total() }} abonné(s)</p>

<div class="uk-card uk-card-default uk-margin-top" style="overflow-x:auto; padding:0;">
    <table class="uk-table uk-table-divider uk-table-middle" style="margin:0;">
        <thead>
            <tr>
                <th>E-mail</th>
                <th>Inscrit le</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subscribers as $subscriber)
                <tr>
                    <td style="font-weight:600;">{{ $subscriber->email }}</td>
                    <td class="uk-text-muted">{{ optional($subscriber->subscribed_at)->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="2" class="uk-text-center uk-text-muted" style="padding:32px 0;">Aucun abonné.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="uk-margin-top">{{ $subscribers->links() }}</div>
@endsection
