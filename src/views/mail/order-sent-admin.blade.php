<!DOCTYPE html>
<?php use Palmabit\Catalog\Models\Product; ?>
<?php $profile_info = (object)Session::get('profile_info'); ?>
<?php $body['order'] = Session::get($body['session_order_key']); ?>
<html lang="it">
<head>
    <meta charset="utf-8">
</head>
<body>
<h2>{{Config::get('authentication::app_name')}}: Ordine ricevuto</h2>
<div>
    Grazie, {{ $body['email'] }}
    <strong>Ordine numero {{$body['order']->id}}</strong>
    <br/>
    <h3>Dettagli cliente: </h3>
    <?php $user_profile = $body['user_profile']; ?>
    @if($user_profile)
    <ul>
        <li>
            <strong>Codice cliente: </strong> {{$user_profile->code}}
        </li>
        <li>
            <strong>Tipo utente: </strong> {{$user_profile->profile_type}}
       </li>
        <li>
            <strong>Ragione sociale: </strong> {{$user_profile->company}}
        </li>
    </ul>
    @else
      <h3>Dettagli non disponibili.</h3>
    @endif
    <h3>Dettagli ordine: </h3>
    @foreach($body['order']->getRowOrders() as $order)
        <ul>
            <?php $product = Product::find($order->product_id); ?>
            <li>
                <strong>Nome: </strong>{{$product->name}}
            </li>
            <li>
                <strong>Code: </strong>{{$product->code}}
            </li>
            <li>
                <strong>Quantità: </strong>{{$order->quantity}}
            </li>
            <li>
                <strong>Prezzo unitario: </strong>{{round($order->total_price / $order->quantity, 2)}}
            </li>
            <li>
                <strong>Prezzo totale: </strong>{{$order->total_price}}
            </li>
            <li>
                <strong>Tipo prezzo applicato: {{L::t($order->price_type_used)}}</strong>
            </li>
        </ul>

    @endforeach
    <hr>
    <h3>{{L::t('Billing')}}</h3>
    <ul>
        <li>{{L::t('Billing address')}}: {{$profile_info->billing_address}}, {{$profile_info->billing_address_zip}} {{$profile_info->billing_city}} {{$profile_info->billing_state}} {{$profile_info->billing_country}}</li>
        <li>{{L::t('VAT')}}: {{$profile_info->vat}}</li>
    </ul>
    <hr>
    <h3>{{L::t('Shipping')}}</h3>
    <ul>
        <li>{{L::t('Shipping address')}}: {{$profile_info->shipping_address}}, {{$profile_info->shipping_address_zip}} {{$profile_info->shipping_city}} {{$profile_info->shipping_state}} {{$profile_info->shipping_country}}</li>
    </ul>
    <br>
</div>
</body>
</html>