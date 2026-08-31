<div class="crud-card__content">

    <div class="form-group">
        <label><strong>Nome</strong></label>
        <div>{{ $user->name ?? $user->nome ?? '—' }}</div>
    </div>

    <div class="form-group" style="margin-top:10px;">
        <label><strong>Email</strong></label>
        <div>{{ $user->email ?? '—' }}</div>
    </div>

    <div class="form-group" style="margin-top:10px;">
        <label><strong>Perfil</strong></label>
        <div>
            @if($user->is_admin || $user->isAdmin())
                <span class="badge-admin">Administrador</span>
            @else
                <span class="badge-user">Usuário comum</span>
            @endif
        </div>
    </div>

    <div class="action-group" style="margin-top:20px; display:flex; gap:10px; flex-wrap:wrap;">

        {{-- VOLTAR --}}
        <a href="{{ route('admin.usuarios.index') }}" class="action-btn">
            ← Voltar
        </a>

        {{-- HISTÓRICO --}}
        <a href="{{ route('admin.usuarios.atividades', $user) }}" class="action-btn">
            Ver histórico
        </a>

        {{-- TOGGLE ADMIN --}}
        @if(Auth::id() !== $user->id)
            <form action="{{ route('admin.usuarios.toggleAdmin', $user) }}" method="POST">
                @csrf
                @method('PATCH')

                <button type="submit" class="action-btn action-admin">
                    {{ $user->is_admin ? 'Remover Admin' : 'Tornar Admin' }}
                </button>
            </form>
        @endif

        {{-- EXCLUIR --}}
        @if(Auth::id() !== $user->id)
            <form action="{{ route('admin.usuarios.destroy', $user) }}" method="POST"
                  onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
                @csrf
                @method('DELETE')

                <button type="submit" class="action-btn action-delete">
                    Excluir
                </button>
            </form>
        @endif

    </div>
</div>
