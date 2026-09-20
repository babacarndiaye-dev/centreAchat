@extends('layouts.admin')

@section('title', 'Modèles de messages')

@section('content')
<p class="text-sm text-terroir-dark/50">Personnalisez les messages envoyés pour chaque événement et chaque canal. Les canaux SMS et WhatsApp nécessitent une passerelle configurée avant activation.</p>

<div class="mt-6 flex flex-col gap-6">
    @foreach($templates as $eventKey => $group)
        <div class="admin-card overflow-hidden p-0">
            <div class="border-b border-terroir-green/10 bg-terroir-cream px-5 py-3">
                <h3 class="font-display text-sm font-semibold">{{ \App\Support\Notifications\NotificationEvents::label($eventKey) }}</h3>
            </div>
            <table class="admin-table">
                <tbody>
                    @foreach($group as $template)
                        <tr>
                            <td class="w-32 pl-6 font-semibold text-terroir-dark">{{ \App\Support\Notifications\NotificationEvents::CHANNELS[$template->channel] ?? $template->channel }}</td>
                            <td class="text-terroir-dark/60">
                                @if($template->is_active)
                                    <span class="text-xs font-semibold text-terroir-green">Actif</span>
                                @else
                                    <span class="text-xs font-semibold text-terroir-dark/40">Inactif</span>
                                @endif
                            </td>
                            <td class="pr-6 text-right">
                                <a href="{{ route('admin.notifications.templates.edit', $template) }}" class="admin-link">Modifier</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach
</div>
@endsection
