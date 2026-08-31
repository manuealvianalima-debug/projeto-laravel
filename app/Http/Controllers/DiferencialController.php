<?php

namespace App\Http\Controllers;

use App\Models\Diferencial;
use Illuminate\Http\Request;

class DiferencialController extends Controller
{
    public function index(Request $request)
    {
        $idiomaSelecionado = old('idioma', $request->get('idioma', 'pt-br'));

        $idiomaId = match ($idiomaSelecionado) {
            'en' => 2,
            default => 1,
        };

        // AJUSTE: Filtramos apenas os diferenciais do tipo 'padrao' para o dropdown oficial
        $diferenciais = Diferencial::where('id_idioma', $idiomaId)
            ->where('tipo', 'padrao') 
            ->get();

        return view('diferenciais.index', compact('diferenciais'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'icone' => 'nullable|string|max:255',
            'id_idioma' => 'required|integer',
        ]);
        
        // AJUSTE: Forçamos o tipo como 'personalizado' para itens criados via formulário
        $validated['tipo'] = 'personalizado';
        
        Diferencial::create($validated);
        
        return response()->json(['success' => true]);
    }
    
    public function update(Request $request, Diferencial $diferencial)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'icone' => 'nullable|string|max:255',
            'id_idioma' => 'required|integer',
            'tipo' => 'nullable|string|in:padrao,personalizado', // Opcional: permite editar o tipo se necessário
        ]);
        
        $diferencial->update($validated);
        
        return response()->json(['success' => true]);
    }
    
    public function destroy(Diferencial $diferencial)
    {
        $diferencial->delete();
        
        return response()->json(['success' => true]);
    }
}
