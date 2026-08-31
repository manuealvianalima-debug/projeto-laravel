@extends('layouts.app')

@section('content')
    <h1>Usuários cadastrados</h1>

    <ul>
        @foreach ($usuarios as $usuario)
            <li>
                <a href="{{ route('admin.usuarios.show', $usuario->id) }}">
                    {{ $usuario->name }}
                </a>
            </li>
        @endforeach
    </ul>
@endsection