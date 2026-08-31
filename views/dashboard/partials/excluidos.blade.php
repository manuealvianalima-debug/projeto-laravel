<div class="crud-card section {{ (isset($tecnologiasExcluidas) && $tecnologiasExcluidas->isNotEmpty()) ? '' : 'hidden' }}" id="excluidos">

    <div class="crud-card__header">
        <div>
            <h3 class="crud-card__title">🗑️ Tecnologias Excluídas</h3>
            <p class="crud-card__desc">
                Registro de tecnologias removidas do sistema. Somente administradores podem ver esta lista.
            </p>
        </div>
    </div>

    <div class="table-scroll">
        <table class="crud-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nome da Tecnologia</th>
                    <th>Número do Caso</th>
                    <th>Situação</th>
                    <th>Criador</th>
                    <th>Data de Exclusão</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tecnologiasExcluidas ?? [] as $tecnologia)
                    <tr>
                        <td>{{ $tecnologia->id }}</td>
                        <td>{{ $tecnologia->titulo ?? $tecnologia->nome ?? '—' }}</td>
                        <td>{{ $tecnologia->numero_caso ?? '—' }}</td>
                        <td>{{ $tecnologia->situacao?->nome ?? '—' }}</td>
                        <td>{{ $tecnologia->responsavel?->name ?? '—' }}</td>
                        <td>{{ optional($tecnologia->deleted_at)->format('d/m/Y H:i') }}</td>

                        <td class="action-group">
                            <a href="{{ route('technology.show', $tecnologia) }}" class="action-btn action-view">
                                Ver
                            </a>

                            <form 
                                action="{{ route('technology.restore', $tecnologia->id) }}"
                                method="POST"
                                onsubmit="return confirm('Tem certeza que deseja restaurar essa tecnologia?');"
                            >
                                @csrf
                                <button type="submit" class="action-btn action-edit">
                                    ♻️ Restaurar
                                </button>
                            </form>
                            <form
                                action="{{ route('technology.forceDelete', $tecnologia->id) }}"
                                method="POST"
                                onsubmit="return confirm('Tem certeza que deseja excluir DEFINITIVAMENTE essa tecnologia? Esta ação é irreversível.');"
                                style="display:inline; margin-left:0.5rem;"
                            >
                                @csrf
                                <button type="submit" class="action-btn action-delete" style="background:#7f1d1d; border-color:#7f1d1d;">
                                    🗡️ Excluir de vez
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="crud-table__empty">
                            Nenhuma tecnologia excluída.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
