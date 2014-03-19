<!DOCTYPE html>
<?php use Palmabit\Catalog\Models\Product; ?>
<html lang="it">
<head>
    <meta charset="utf-8">
</head>
<body>
<h2>Ordine su {{Config::get('authentication::app_name')}} eseguito con successo</h2>
<div>
    Buongiorno {{ $body['email'] }}
    <strong>L'ordine numero: {{$body['order']->id}} è stato inoltrato con successo.</strong>
    <br/>
    <strong>Dettagli ordine:</strong>
    <ul>
        @foreach($body['order']->row_orders()->get() as $key => $order)
            <? $product = Product::find($order->product_id); ?>
            <li>
                <strong>nome: </strong>{{$product->name}}
            </li>
        @endforeach
    </ul>
    <a href="{{URL::to('/')}}" target="_blank">Vai al sito</a>
</div>
</body>
</html>