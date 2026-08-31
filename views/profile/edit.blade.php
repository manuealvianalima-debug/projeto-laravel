<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Perfil - Portfólio de Inovação</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700;800&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body class="antialiased">
    @include('layouts.header')
    <section class="header-banner header-banner--compact">
        <div class="header-content"><h1>Editar Perfil</h1></div>
        <div class="top-actions"><a href="{{ route('profile.show') }}" class="nav-item">← Perfil</a></div>
    </section>
    <div class="dashboard-layout">
        <aside class="sidebar">
            <a href="{{ route('profile.show') }}" class="menu-item">👤 Perfil</a>
            <a href="{{ route('profile.edit') }}" class="menu-item active">✏️ Editar</a>
        </aside>
        <main class="main-panel">
            @if ($errors->any())<div class="form-alert form-alert--error"><ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
            <form action="{{ route('profile.update') }}" method="POST" class="crud-card">
                @csrf @method('PUT')
                <div class="form-field"><label class="form-label" for="name">Nome *</label><input type="text" id="name" name="name" class="form-input" required value="{{ old('name', $user->name ?? $user->nome) }}"></div>
                <div class="form-field"><label class="form-label" for="email">E-mail *</label><input type="email" id="email" name="email" class="form-input" required value="{{ old('email', $user->email) }}"></div>
                <div class="form-field"><label class="form-label" for="descricao">Descrição</label><textarea id="descricao" name="descricao" class="form-textarea" rows="4">{{ old('descricao', $user->descricao) }}</textarea></div>
                <div class="form-actions form-actions--inline">
                    <button type="submit" class="btn-form btn-form--secondary">Salvar</button>
                    <a href="{{ route('profile.show') }}" class="btn-form btn-form--outline">Cancelar</a>
                </div>
            </form>
        </main>
    </div>
    @include('layouts.footer')
</body>
</html>
