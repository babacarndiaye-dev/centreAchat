@extends('layouts.admin')

@section('title', 'Modèles de messages')

@section('content')
<p class="uk-text-small uk-text-muted">Personnalisez les messages envoyés pour chaque événement et chaque canal. Les canaux SMS et WhatsApp nécessitent une passerelle configurée avant activation.</p>

<div class="uk-margin-top" style="display:flex; flex-direction:column; gap:24px;">
    @foreach($templates as $eventKey => $group)
        <div class="uk-card uk-card-default" style="padding:0; overflow:hidden;">
            <div style="border-bottom:1px solid rgba(29,138,78,.1); background:#F7F8F5; padding:12px 20px;">
                <h3 style="font-family:'Fraunces',serif; font-weight:600; font-size:.875rem;">{{ \App\Support\Notifications\NotificationEvents::label($eventKey) }}</h3>
            </div>
            <table class="uk-table uk-table-divider uk-table-middle" style="margin:0;">
                <tbody>
                    @foreach($group as $template)
                        <tr>
                            <td style="width:8rem; font-weight:600;">{{ \App\Support\Notifications\NotificationEvents::CHANNELS[$template->channel] ?? $template->channel }}</td>
                            <td class="uk-text-muted">
                                @if($template->is_active)
                                    <span style="font-size:.75rem; font-weight:600; color:#1D8A4E;">Actif</span>
                                @else
                                    <span style="font-size:.75rem; font-weight:600; color:rgba(31,35,40,.4);">Inactif</span>
                                @endif
                            </td>
                            <td class="uk-text-right">
                                <a href="{{ route('admin.notifications.templates.edit', $template) }}" style="font-size:.875rem; font-weight:600; color:#1D8A4E;">Modifier</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach
</div>
@endsection
