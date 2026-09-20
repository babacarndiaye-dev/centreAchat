@extends('layouts.admin')

@section('title', 'Modifier le modèle')

@section('content')
<form action="{{ route('admin.notifications.templates.update', $template) }}" method="POST" class="admin-card max-w-2xl">
    @csrf
    @method('PATCH')

    <div class="text-sm text-terroir-dark/50">
        {{ \App\Support\Notifications\NotificationEvents::label($template->event_key) }} —
        {{ \App\Support\Notifications\NotificationEvents::CHANNELS[$template->channel] ?? $template->channel }}
    </div>

    @if(in_array($template->channel, ['email']))
        <div class="mt-4">
            <label class="label">Objet</label>
            <input type="text" name="subject" value="{{ old('subject', $template->subject) }}" class="input">
        </div>
    @endif

    <div class="mt-4">
        <label class="label">Message</label>
        <textarea name="body" rows="6" required class="input">{{ old('body', $template->body) }}</textarea>
    </div>

    @if(count($placeholders))
        <div class="mt-4 rounded-lg bg-terroir-cream px-4 py-3 text-sm text-terroir-dark/50">
            Variables disponibles :
            @foreach($placeholders as $placeholder)
                @php($tag = '{'.'{'.$placeholder.'}'.'}')
                <code class="mx-0.5 rounded bg-white px-1.5 py-0.5">{{ $tag }}</code>
            @endforeach
        </div>
    @endif

    <label class="mt-4 flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $template->is_active)) class="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20">
        Actif
        @if(in_array($template->channel, ['sms', 'whatsapp']))
            <span class="text-xs text-terroir-dark/40">(nécessite une passerelle {{ $template->channel === 'sms' ? 'SMS' : 'WhatsApp' }} configurée)</span>
        @endif
    </label>

    <div class="mt-6">
        <button type="submit" class="btn-primary">Enregistrer</button>
        <a href="{{ route('admin.notifications.templates.index') }}" class="ml-3 text-sm font-semibold text-terroir-dark/60">Annuler</a>
    </div>
</form>
@endsection
