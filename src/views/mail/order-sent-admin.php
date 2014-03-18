<!DOCTYPE html>
<?php use Palmbit\Catalog\Models\Product; ?>
<?php $profile_info = Session::get('profile_info'); ?>
<html lang="it">
<head>
    <meta charset="utf-8">
</head>
<body>
<h2>Ordine su {{Config::get('authentication::app_name')}} inoltrato con successo</h2>
<div>
    Buongiorno {{ $body['email'] }}
    <strong>L'ordine numero: {{$body['order']->id}} è stato inoltrato con successo.</strong>
    <br/>
    <strong>Dettagli ordine:</strong>
    <ul>
        @foreach($body['order']->row_order()->get() as $order)
            <? $product = Product::find($order->product_id); ?>
            <li>
                <strong>nome: </strong>{{$product->name}}
            </li>
        @endforeach
    </ul>
    {{-- i dettagli della spedizione sono in profile_info e sono uguali all'input del form dove si può modificare indirizzo spedizione e billing --}}
    <a href="{{URL::to('/')}}" target="_blank">Vai al tuo pannello per i dettagli</a>
</div>
</body>
</html>