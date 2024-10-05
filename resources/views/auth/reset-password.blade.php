@extends('layouts.passform')

@section('title')
Cambiar contraseña < EasyShop
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <h3>Cambiar contraseña</h3>
        <p>Introduce tu email y tu nueva contraseña.</p>

        <form class="form-container" method="post" action="{{ route('password.update') }}">
            @csrf
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Correo electrónico: *</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Nueva contraseña: *</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Confirmar constraseña: *</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <div class="mb-3">
                <input type="hidden" name="token" value="{{ $token }}" class="form-control" required>
            </div>
            <div class="buttons">
                <button type="submit" class="btn btn-submit">Enviar</button>
            </div>
            
        </form>
    </div>
</div>
@endsection