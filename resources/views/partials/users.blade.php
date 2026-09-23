<section class="panel" data-panel="users" hidden>
    <header class="panel-head"><div><p class="eyebrow">Administration</p><h2>Utilisateurs & droits</h2><p>Les équipes accèdent uniquement avec un compte créé ici.</p></div></header>
    <div class="surface">
        <div class="surface-title"><div><h3>Créer un compte</h3><p>Utilisez un mot de passe provisoire d’au moins 12 caractères.</p></div></div>
        <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
            @csrf<input type="hidden" name="active" value="1">
            <div class="field-grid lg:grid-cols-3"><label class="field"><span>Nom complet</span><input class="input" name="name" required></label><label class="field"><span>Email</span><input class="input" type="email" name="email" required></label><label class="field"><span>Rôle</span><select class="select" name="role">@foreach(['admin'=>'Administrateur','direction'=>'Direction','communication_owner'=>'Responsable Communication','viewer'=>'Lecture seule'] as $value=>$label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select></label></div>
            <div class="field-grid"><label class="field"><span>Mot de passe provisoire</span><input class="input" type="password" name="password" minlength="12" required></label><label class="field"><span>Confirmation</span><input class="input" type="password" name="password_confirmation" minlength="12" required></label></div>
            <div class="flex justify-end"><button class="btn btn-primary"><i data-lucide="user-plus"></i>Créer le compte</button></div>
        </form>
    </div>
    <div class="space-y-3">
        @foreach($users as $user)
            <article class="surface">
                <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-4">
                    @csrf @method('PUT')
                    <div class="field-grid lg:grid-cols-4">
                        <label class="field"><span>Nom</span><input class="input" name="name" value="{{ $user->name }}" required></label>
                        <label class="field"><span>Email</span><input class="input" type="email" name="email" value="{{ $user->email }}" required></label>
                        <label class="field"><span>Rôle</span><select class="select" name="role">@foreach(['admin'=>'Administrateur','direction'=>'Direction','communication_owner'=>'Responsable Communication','viewer'=>'Lecture seule'] as $value=>$label)<option value="{{ $value }}" @selected($user->role === $value)>{{ $label }}</option>@endforeach</select></label>
                        <label class="field"><span>État</span><select class="select" name="active" @disabled($user->id === auth()->id())><option value="1" @selected($user->active)>Actif</option><option value="0" @selected(!$user->active)>Désactivé</option></select>@if($user->id === auth()->id())<input type="hidden" name="active" value="1">@endif</label>
                    </div>
                    <div class="field-grid"><label class="field"><span>Nouveau mot de passe (facultatif)</span><input class="input" type="password" name="password" minlength="12"></label><label class="field"><span>Confirmation</span><input class="input" type="password" name="password_confirmation" minlength="12"></label></div>
                    <div class="flex justify-end"><button class="btn btn-secondary"><i data-lucide="save"></i>Mettre à jour</button></div>
                </form>
                @if($user->id !== auth()->id())
                    <form method="POST" action="{{ route('users.destroy', $user) }}" class="mt-3 flex justify-end" onsubmit="return confirm('Supprimer définitivement ce compte ?')">@csrf @method('DELETE')<button class="btn btn-danger"><i data-lucide="user-x"></i>Supprimer</button></form>
                @endif
            </article>
        @endforeach
    </div>
</section>
