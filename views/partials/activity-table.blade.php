@php
    $showUser = $showUser ?? false;
@endphp

@if(!empty($migrationPending))
    <div class="form-alert form-alert--error">
        A tabela de logs ainda não foi criada. Execute: <code>php artisan migrate</code>
    </div>
@endif

<div class="table-scroll">
    <table class="crud-table crud-table--users">
        <thead>
            <tr>
                @if($showUser)
                    <th>Usuário</th>
                @endif
                <th>Tipo</th>
                <th>Atividade</th>
                <th>Data</th>
            </tr>
        </thead>

        <tbody>
            @forelse($activities as $log)
                <tr>
                    @if($showUser)
                        <td>
                            {{ optional($log->user)->name 
                                ?? optional($log->user)->nome 
                                ?? '—' }}

                            <br>

                            <small class="action-group__hint">
                                {{ optional($log->user)->email ?? '—' }}
                            </small>
                        </td>
                    @endif

                    <td>
                        <span class="badge-admin badge-admin--sm">
                            {{ $log->action ?? '—' }}
                        </span>
                    </td>

                    <td>{{ $log->description ?? '—' }}</td>

                    <td>
                        {{ $log->created_at ? $log->created_at->format('d/m/Y H:i') : '—' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $showUser ? 4 : 3 }}" class="crud-table__empty">
                        Nenhuma atividade registrada ainda.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if(is_object($activities) && method_exists($activities, 'links'))
    <div style="margin-top:1rem;">
        {{ $activities->links() }}
    </div>
@endif