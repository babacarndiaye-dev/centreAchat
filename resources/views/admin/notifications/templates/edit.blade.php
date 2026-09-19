@extends('layouts.admin')

@section('title', 'Modifier le modèle')

@section('content')
<form action="{{ route('admin.notifications.templates.update', $template) }}" method="POST" class="uk-card uk-card-default" style="max-width:40rem; padding:32px;">
    @csrf
    @method('PATCH')

    <div class="uk-text-small uk-text-muted">
        {{ \App\Support\Notifications\NotificationEvents::label($template->event_key) }} —
        {{ \App\Support\Notifications\NotificationEvents::CHANNELS[$template->channel] ?? $template->channel }}
    </div>

    @if(in_array($template->channel, ['email']))
        <div class="uk-margin-top">
            <label class="uk-form-label">Objet</label>
            <input type="text" name="subject" value="{{ old('subject', $template->subject) }}" class="uk-input">
        </div>
    @endif

    <div class="uk-margin-top">
        <label class="uk-form-label">Message</label>
        <textarea name="body" rows="6" required class="uk-textarea">{{ old('body', $template->body) }}</textarea>
    </div>

    @if(count($placeholders))
        <div class="uk-margin-top uk-text-small uk-text-muted" style="background:#F7F8F5; border-radius:8px; padding:12px 16px;">
            Variables disponibles :
            @foreach($placeholders as $placeholder)
                @php($tag = '{'.'{'.$placeholder.'}'.'}')
                <code style="margin:0 2px; background:#fff; border-radius:4px; padding:2px 6px;">{{ $tag }}</code>
            @endforeach
        </div>
    @endif

    <label class="uk-flex uk-flex-middle uk-text-small uk-margin-top" style="gap:8px;">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $template->is_active)) class="uk-checkbox">
        Actif
        @if(in_array($template->channel, ['sms', 'whatsapp']))
            <span style="font-size:.75rem; color:rgba(31,35,40,.4);">(nécessite une passerelle {{ $template->channel === 'sms' ? 'SMS' : 'WhatsApp' }} configurée)</span>
        @endif
    </label>

    <div class="uk-margin-top">
        <button type="submit" class="uk-button uk-button-primary">Enregistrer</button>
        <a href="{{ route('admin.notifications.templates.index') }}" style="margin-left:12px; font-size:.875rem; font-weight:600; color:rgba(31,35,40,.6);">Annuler</a>
    </div>
</form>
@endsection
