@extends('layouts.passform')

@section('title')
Contraseña olvidada < EasyShop
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <h3>Recuperar contraseña</h3>
        <p>Introduce tu email y recibirás un correo para reestablecerla.</p>

        <form class="form-container" method="post" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Correo electrónico: *</label>
                <input type="email" name="email" class="form-control" id="exampleInputEmail1" required>
            </div>
            <div class="buttons">
                <button type="submit" class="btn btn-submit">Enviar</button>
            </div>
            
        </form>
    </div>
</div>
@endsection