<div class="crud-card" id="rascunhos">
    <div class="crud-card__header">
        <div>
            <h3 class="crud-card__title">📝 Meus Rascunhos</h3>
            <p class="crud-card__desc">Tecnologias que você ainda está desenvolvendo.</p>
        </div>
        <a href="{{ route('technology.index') }}" class="action-btn action-view">
            ➕ Novo Rascunho
        </a>
    </div>

    <div class="table-scroll">
        <table class="crud-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nome</th>
                    <th>Número</th>
                    <th>Situação</th>
                    <th>Atualizado</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rascunhos as $tecnologia)
                    <tr>
                        <td>{{ $tecnologia->id }}</td>
                        <td>{{ $tecnologia->titulo ?? $tecnologia->nome ?? '—' }}</td>
                        <td>{{ $tecnologia->numero_caso ?? '—' }}</td>
                        <td>{{ $tecnologia->situacao?->nome ?? '—' }}</td>
                        <td>{{ optional($tecnologia->updated_at)->format('d/m/Y H:i') }}</td>
                        <td class="action-group">
                            <a href="{{ route('technology.show', $tecnologia) }}" class="action-btn action-view">Ver</a>
                            @if(Auth::user()->isAdmin() || Auth::user()->email === 'manuela.viana@fiocruz.br' || Auth::user()->id === ($tecnologia->id_user_criador ?? $tecnologia->user_id ?? null))
                                <a href="{{ route('technology.edit', $tecnologia) }}" class="action-btn action-edit">Editar</a>
                                <form action="{{ route('technology.destroy', $tecnologia) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir essa tecnologia?');" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn action-delete">Excluir</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">Você não tem rascunhos.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
