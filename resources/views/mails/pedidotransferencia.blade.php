<p>¡Gracias por tu compra! A continuación te mostramos tu pedido en EasyShop.</p>

<p>Recuerda que para poder confirmar el pedido, debes realizar la transferencia a nuestra cuenta bancaria. Estos son los datos:</p>

<span>
    <strong>Referencia del pedido:</strong> #{{ $referencia }}
</span>

<br>

<span>
    <strong>Nombre de la cuenta:</strong> {{ $nombre_cuenta }}
</span>

<br>

<span>
    <strong>Nombre del banco:</strong> {{ $banco }}
</span>

<br>

<span>
    <strong>IBAN:</strong> {{ $iban }}
</span>

<br>

<span>
    <strong>BIC/SWIFT:</strong> {{ $bic_swift }}
</span>

<br>

<p>Este es el contenido de tu pedido:</p>

@foreach ( $productos as $producto )
    
<span>
    <strong>{{ $producto['nombre_producto'] }}</strong>: {{ $producto['subtotal'] }} x {{ $producto['cantidad']}} = {{ $producto['total'] }}€ <br>
</span>

@endforeach

<span>
    <strong>Método de envío:</strong> {{ $metodo_envio }} - {{ $gastos_envio }}€
</span>

<br>

<span>
    <strong>Descuento:</strong> - {{ $descuento ? $descuento : 0.00 }}{{ $tipo_descuento }}
</span>

<br>

<span>
    <strong>Total:</strong> {{ $total }}€
</span>