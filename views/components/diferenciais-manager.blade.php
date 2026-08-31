@props([
    'idiomaId' => 1,
    'icones' => [],
    'diferenciaisDisponiveis' => [],
    'oldDiferenciais' => [],
    'selectedDiferenciais' => [],
])

@php
    $iconNames = collect($icones)->pluck('name')->filter()->unique()->values()->all();
    if (empty($iconNames) && is_array($icones)) { $iconNames = $icones; }

    $padroes = collect($diferenciaisDisponiveis)
        ->filter(fn($i) => (is_array($i) ? ($i['tipo'] ?? null) : $i->tipo) === 'padrao')
        ->map(fn($i) => [
            'id' => is_array($i) ? ($i['id'] ?? 0) : $i->id,
            'nome' => is_array($i) ? ($i['nome'] ?? '') : $i->nome,
            'icone' => is_array($i) ? ($i->icone ?? 'star') : ($i->icone ?? 'star'),
            'tipo' => 'padrao'
        ])->values()->all();

   $iniciais = collect($oldDiferenciais)->isNotEmpty() ? $oldDiferenciais : $selectedDiferenciais;
    $iniciais = collect($iniciais)->map(fn($i) => [
        'id' => is_array($i) ? ($i['id'] ?? null) : ($i->id ?? null),
        'nome' => is_array($i) ? ($i['nome'] ?? $i['titulo'] ?? '') : ($i->nome ?? $i->titulo ?? ''),
        'icone' => is_array($i) ? ($i['icone'] ?? 'star') : ($i->icone ?? 'star'),
        'tipo' => is_array($i) ? ($i['tipo'] ?? 'padrao') : ($i->tipo ?? 'padrao'),
    ])->values()->all();
@endphp

<div x-data='{
    showDropdown: false,
    showModal: false,
    searchQuery: "",
    selectedDiferenciais: @json($iniciais),
    availableDiferenciais: @json($padroes),
    custom: { nome: "", icone: "help" },
    selectedLabel: "{{ __('technology.selected_label') }}",
    maxMessage: "{{ __('technology.max_3_differentials') }}",
    enterNameMessage: "{{ __('technology.enter_name') }}",
    searchPlaceholder: "{{ __('technology.search_differential_placeholder') }}",
    createCustomLabel: "{{ __('technology.create_custom_differential') }}",
    noneSelectedMessage: "{{ __('technology.none_selected_differentials') }}",
    newDifferentialLabel: "{{ __('technology.new_differential') }}",
    differentialExample: "{{ __('technology.differential_example') }}",
    defaultIconLabelText: "{{ __('technology.default_icon_label') }}",
    addButtonLabel: "{{ __('technology.add') }}",
    get filteredPadrao() {
        if (!this.searchQuery) return this.availableDiferenciais;
        let q = this.searchQuery.toLowerCase();
        return this.availableDiferenciais.filter(d => d.nome.toLowerCase().includes(q));
    },
    
    add(diff) {
        if (this.selectedDiferenciais.length >= 3) { alert(this.maxMessage); return; }
        if (diff.id && this.selectedDiferenciais.some(s => s.id == diff.id)) return;
        this.selectedDiferenciais.push({...diff});
        this.showDropdown = false;
        this.searchQuery = "";
    },
    
    saveCustom() {
        if (!this.custom.nome.trim()) { alert(this.enterNameMessage); return; }
        this.add({
            id: null,
            nome: this.custom.nome,
            icone: this.custom.icone || "help",
            tipo: "personalizado"
        });
        this.custom = { nome: "", icone: "help" };
        this.showModal = false;
    },
    
    remove(idx) {
        this.selectedDiferenciais.splice(idx, 1);
    }
}' class="form-section">
    
    <h3 class="form-section-title"><b>{{ __('technology.differentials') }}</b></h3>
    
    <div class="custom-select-wrapper">
                <button type="button" class="custom-select-button" @click="showDropdown = !showDropdown">
            <div class="selected-option-display">
                <span x-show="selectedDiferenciais.length === 0">{{ __('technology.select_differential') }}</span>
                <template x-show="selectedDiferenciais.length > 0">
                    <span class="material-symbols-outlined" x-text="selectedDiferenciais[0].icone"></span>
                    <span x-text="selectedDiferenciais[0].nome"></span>
                </template>
            </div>
            <span class="material-symbols-outlined select-arrow" :class="{ 'rotated': showDropdown }">expand_more</span>
        </button>

        <div x-show="showDropdown" x-cloak class="custom-select-dropdown" @click.away="showDropdown = false">
            <div class="dropdown-search">
                <span class="material-symbols-outlined">search</span>
                <input type="text" class="dropdown-search-input" x-model="searchQuery" :placeholder="searchPlaceholder">
            </div>
            
            <div class="dropdown-options">
                <template x-for="diff in filteredPadrao" :key="diff.id">
                    <div class="dropdown-option" @click="add(diff)">
                        <span class="material-symbols-outlined" x-text="diff.icone"></span>
                        <span x-text="diff.nome"></span>
                        <span class="check-mark" x-show="selectedDiferenciais.some(s => s.id == diff.id)">check</span>
                    </div>
                </template>
                
                <div class="dropdown-option option-create" @click="showDropdown = false; showModal = true">
                    <span class="material-symbols-outlined">add</span>
                    <span x-text="createCustomLabel"></span>
                </div>
            </div>
        </div>
    </div>

   <div class="selected-diferenciais-list">
    <div x-show="selectedDiferenciais.length > 0" class="form-counter" x-text="selectedLabel + selectedDiferenciais.length + '/3'"></div>
    
    <div class="diferenciais-grid">
        <template x-for="(diff, idx) in selectedDiferenciais" :key="idx">
            <div class="diferencial-card selected">
                <div class="diferencial-card-content">
                    <div class="diferencial-icon">
                        <span class="material-symbols-outlined" x-text="diff.icone"></span>
                    </div>
                    <div class="diferencial-info">
                        <div class="diferencial-nome" x-text="diff.nome"></div>
                        <div class="diferencial-tipo" x-text="diff.tipo === 'padrao' ? 'Padrão' : 'Personalizado'"></div>
                    </div>
                    <div class="diferencial-actions">
                        <button type="button" class="btn-remove" @click="remove(idx)">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                    
                    <input type="hidden" :name="'diferenciais['+idx+'][id]'" :value="diff.id">
                    <input type="hidden" :name="'diferenciais['+idx+'][nome]'" :value="diff.nome">
                    <input type="hidden" :name="'diferenciais['+idx+'][icone]'" :value="diff.icone">
                    <input type="hidden" :name="'diferenciais['+idx+'][tipo]'" :value="diff.tipo">
                </div>
            </div>
        </template>
        
        <!-- Mensagem quando nenhum diferencial selecionado -->
        <div x-show="selectedDiferenciais.length === 0" style="grid-column: 1 / -1; text-align: center; color: #94a3b8; padding: 2rem;">
            <span x-text="noneSelectedMessage"></span>
        </div>
    </div>
</div>

    <!-- Modal Centralizado -->
    <div x-show="showModal" x-cloak class="modal-overlay" @click.away="showModal = false">
        <div class="modal-content" @click.away="showModal = false">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('technology.new_differential') }}</h4>
                <button type="button" class="modal-close" @click="showModal = false">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <div class="modal-body">
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.875rem; margin-bottom: 0.5rem; font-weight: 600; color: #475569;">
                        {{ __('technology.differentials') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="dropdown-search-input" x-model="custom.nome" :placeholder="differentialExample">
                </div>
                
                <div>
                    <label style="display: block; font-size: 0.875rem; margin-bottom: 0.5rem; font-weight: 600; color: #475569;">Ícone</label>
                    <div class="diferencial-icone-row">
                        <div class="diferencial-icone-preview">
                            <span class="material-symbols-outlined" x-text="custom.icone"></span>
                        </div>
                        
                        <select x-model="custom.icone" class="dropdown-search-input">
                            <option value="help">{{ __('technology.default_icon_label') }}</option>
                            @foreach($iconNames as $icon)
                                <option value="{{ $icon }}">{{ $icon }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-form btn-form--outline" @click="showModal = false">
                    {{ __('technology.cancel') }}
                </button>
                <button type="button" class="btn-form btn-form--primary" @click="saveCustom()">
                    <span x-text="addButtonLabel"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }

    /* Custom Select */
    .custom-select-wrapper {
        position: relative;
        width: 100%;
    }

    .custom-select-button {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.625rem 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem;
        background: white;
        font-size: 0.875rem;
        color: #1e293b;
        cursor: pointer;
        transition: all 0.2s;
    }

    .custom-select-button:hover {
        border-color: #cbd5e1;
    }

    .custom-select-button:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .selected-option-display {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .selected-option-display .material-symbols-outlined {
        font-size: 1.25rem;
        color: #4a5568;
    }

    .select-arrow {
        font-size: 1.25rem;
        color: #64748b;
        transition: transform 0.2s;
    }

    .select-arrow.rotated {
        transform: rotate(180deg);
    }

    .custom-select-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        margin-top: 0.25rem;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        max-height: 350px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .dropdown-search {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .dropdown-search .material-symbols-outlined {
        font-size: 1.25rem;
        color: #64748b;
    }

    .dropdown-search-input {
        flex: 1;
        border: none;
        outline: none;
        font-size: 0.875rem;
    }

    .dropdown-search-input::placeholder {
        color: #94a3b8;
    }

    .dropdown-options {
        overflow-y: auto;
        max-height: 280px;
    }

    .dropdown-option {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.5rem 0.75rem;
        cursor: pointer;
        transition: background 0.15s;
    }

    .dropdown-option:hover {
        background: #f1f5f9;
    }

    .dropdown-option .material-symbols-outlined {
        font-size: 1.25rem;
        color: #4a5568;
    }

    .check-mark {
        margin-left: auto;
        color: #3b82f6;
        font-size: 1rem;
    }

    .option-create {
        color: #3b82f6;
        font-weight: 500;
    }

    .option-create .material-symbols-outlined {
        color: #3b82f6;
    }

    /* Selected list */
    .selected-diferenciais-list {
        margin-top: 1rem;
    }

    .diferenciais-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 0.75rem;
    }

    .diferencial-card {
        border: 2px solid #e2e8f0;
        border-radius: 0.5rem;
        background: white;
        transition: all 0.2s;
    }

    .diferencial-card:hover {
        border-color: #cbd5e1;
    }

    .diferencial-card.selected {
        border-color: #3b82f6;
        background: #eff6ff;
    }

    .diferencial-card-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
    }

    .diferencial-icon {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        border-radius: 0.5rem;
    }

    .diferencial-icon .material-symbols-outlined {
        font-size: 1.5rem;
        color: #4a5568;
    }

    .diferencial-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 0.125rem;
    }

    .diferencial-nome {
        font-size: 0.875rem;
        font-weight: 600;
        color: #1e293b;
    }

    .diferencial-tipo {
        font-size: 0.75rem;
        color: #64748b;
    }

    .diferencial-actions {
        flex-shrink: 0;
    }

    .btn-remove {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border: none;
        background: transparent;
        color: #94a3b8;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-remove:hover {
        background: #fee2e2;
        color: #ef4444;
    }

    /* Modal - CENTRALIZADO */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 999999;
        padding: 1rem;
    }

    .modal-content {
        background: white;
        border-radius: 0.75rem;
        width: 90%;
        max-width: 450px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .modal-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
    }

    .modal-close {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border: none;
        background: transparent;
        color: #64748b;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .modal-close:hover {
        background: #f1f5f9;
        color: #1e293b;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        padding: 1rem 1.5rem;
        border-top: 1px solid #e2e8f0;
    }

    .diferencial-icone-row {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .diferencial-icone-preview {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        border-radius: 0.5rem;
        border: 1px solid #e2e8f0;
    }

    .diferencial-icone-preview .material-symbols-outlined {
        font-size: 1.75rem;
        color: #4a5568;
    }

    /* Botões */
    .btn-form {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }

    .btn-form--outline {
        background: transparent;
        border: 1px solid #cbd5e1;
        color: #475569;
    }

    .btn-form--outline:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
    }

    .btn-form--primary {
        background: #3b82f6;
        border: 1px solid #3b82f6;
        color: white;
    }

    .btn-form--primary:hover {
        background: #2563eb;
        border-color: #2563eb;
    }

    .text-danger { color: #ef4444; }
    .form-hint { font-size: 0.75rem; color: #64748b; margin-top: 0.25rem; }
    .form-counter { font-size: 0.75rem; color: #94a3b8; text-align: right; margin-top: 0.25rem; }
</style>