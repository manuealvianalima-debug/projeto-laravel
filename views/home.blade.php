<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portfólio de Inovação</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Raleway:wght@400;600;700;800&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{ asset('js/diferenciais.js') }}"></script>
</head>
<body class="antialiased">
    <!-- Botões de autenticação no topo direito -->
    <div style="position: absolute; top: 2.5rem; right: 0; padding: 1.5em; z-index: 10;">
        @auth
            <span style="display: inline-block; color: white; margin-right: 10px; margin-top: 0.4rem;">Olá, {{ Auth::user()->name }}!</span>
            <a href="{{ route('dashboard') }}" class="nav-item">🏠 Dashboard</a>
            <a href="{{ route('logout') }}" class="nav-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">🚪 Sair</a>
            <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">@csrf</form>
        @else
            <a href="{{ route('login') }}" class="nav-item">Login</a>
            <a href="{{ route('admin.login') }}" class="nav-item">Admin</a>
        @endauth
    </div>

    @include('layouts.header')

    <!-- Header Banner -->
    <div class="header-banner header-banner--normal">
        <div class="header-content">
            <h1>Portfólio de<br>Inovação da Fiocruz</h1>
        </div>
    </div>

    <!-- Navbar -->
    <nav>
        <div class="menu-toggle" onclick="document.querySelector('.nav-links').classList.toggle('open')">
            Menu ▼
        </div>
        <div class="navbar nav-links">
            <a href="{{ url('/') }}" class="nav-item">Início</a>
            <a href="#" class="nav-item">Tecnologias</a>
            <a href="#" class="nav-item">Seja parceiro</a>
            <a href="#" class="nav-item">Ofertas Tecnológicas</a>
            <a href="#" class="nav-item">Núcleo de Inovação Tecnológica</a>
            <a href="#" class="nav-item">Contato</a>
        </div>
    </nav>

    <!-- Conteúdo principal - INTRO SECTION (visível para todos) -->
    <div class="intro-section">
        <div class="intro-text">
            <p>A Fundação Oswaldo Cruz (Fiocruz) empreende esforços para transformar os conhecimentos gerados internamente em produtos e serviços inovadores para atendimento às necessidades da sociedade e do Sistema Único de Saúde (SUS).</p>
            <p>Pensando nisso, o Portfólio de Inovação da Fiocruz reúne tecnologias desenvolvidas na Instituição com alto potencial inovador, que visam contribuir na geração de soluções para problemas de saúde pública.</p>
            <p>Se sua empresa ou instituição também compartilha desse compromisso, venha ser nosso parceiro.</p>
        </div>
        <div class="intro-image">
            <img src="/images/portfoliodna.png" alt="Portfolio DNA">
        </div>
    </div>

    @include('layouts.footer')
</body>
</html>
