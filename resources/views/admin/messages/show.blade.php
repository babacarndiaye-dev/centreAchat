@extends('layouts.admin')

@section('title', 'Message de '.$message->name)

@section('content')
<div class="admin-card max-w-3xl">
    <dl class="space-y-2 text-sm">
        <div><dt class="inline text-terroir-dark/50">Nom : </dt><dd class="inline font-semibold text-terroir-dark">{{ $message->name }}</dd></div>
        <div><dt class="inline text-terroir-dark/50">E-mail : </dt><dd class="inline font-semibold text-terroir-dark">{{ $message->email }}</dd></div>
        @if($message->phone)
            <div><dt class="inline text-terroir-dark/50">Téléphone : </dt><dd class="inline font-semibold text-terroir-dark">{{ $message->phone }}</dd></div>
        @endif
        @if($message->subject)
            <div><dt class="inline text-terroir-dark/50">Sujet : </dt><dd class="inline font-semibold text-terroir-dark">{{ $message->subject }}</dd></div>
        @endif
        <div><dt class="inline text-terroir-dark/50">Date : </dt><dd class="inline font-semibold text-terroir-dark">{{ $message->created_at->format('d/m/Y H:i') }}</dd></div>
    </dl>

    <div class="mt-4 rounded-lg bg-terroir-cream/60 p-4 text-sm leading-relaxed text-terroir-dark/80">
        {{ $message->message }}
    </div>

    @if($message->reply)
        <div class="mt-6 border-t border-terroir-dark/10 pt-6">
            <p class="text-xs font-semibold uppercase tracking-wide text-terroir-green">
                Réponse envoyée{{ $message->replied_at ? ' le '.$message->replied_at->format('d/m/Y à H:i') : '' }}{{ $message->repliedBy ? ' par '.$message->repliedBy->name : '' }}
            </p>
            <div class="mt-2 rounded-lg bg-terroir-green/5 p-4 text-sm leading-relaxed text-terroir-dark/80">{{ $message->reply }}</div>
        </div>
    @endif

    <form action="{{ route('admin.messages.reply', $message) }}" method="POST" class="mt-6 border-t border-terroir-dark/10 pt-6">
        @csrf
        <label class="label" for="reply">{{ $message->reply ? 'Envoyer une nouvelle réponse' : 'Répondre' }}</label>
        <textarea id="reply" name="reply" rows="5" required class="input" placeholder="Votre réponse...">{{ old('reply') }}</textarea>
        <div class="mt-3 flex gap-3">
            <button type="submit" class="btn-primary">
                <span class="material-symbols-outlined text-lg">send</span>
                Envoyer la réponse
            </button>
            <a href="{{ route('admin.messages.index') }}" class="btn-outline">Retour</a>
        </div>
    </form>
</div>
@endsection
