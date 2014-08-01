<!DOCTYPE html>
<?php use Palmabit\Catalog\Models\Product; ?>
<?php $profile_info = (object)Session::get('profile_info'); ?>
<?php $body['order'] = Session::get($body['session_order_key']); ?>
<?php $user_profile = App::make('authenticator')->getLoggedUser()->user_profile()->first(); ?>
<html lang="it">
<head>
    <meta charset="utf-8">
</head>
<body>
<h2>{{Config::get('authentication::app_name')}}: Ordine ricevuto</h2>
<div>
    Grazie, {{ $body['email'] }}<br/>
    <strong>Ordine numero: {{$body['order']->id}}</strong>
    <br/>
    <h3>Dettagli cliente: </h3>
    @if($profile_info)
    <h5>Fatturazione:</h5>
    <ul>
        <li>
            <strong>Codice cliente: </strong> {{isset($profile_info->code) ? $profile_info->code : 'non disponibile'}}
        </li>
        <li>
            <strong>Tipo utente: </strong> {{isset($user_profile->profile_type) ? $user_profile->profile_type : 'Non disponibile'}}
        </li>
        <li>
            <strong>Nome fatturazione: </strong> {{isset($profile_info->billing_first_name) ? $profile_info->billing_first_name : 'Non disponibile'}}
        </li>
        <li>
            <strong>Cognome fatturazione: </strong> {{isset($profile_info->billing_last_name) ? $profile_info->billing_last_name : 'Non disponibile'}}
        </li>
        <li>
            <strong>Ragione sociale fatturazione: </strong> {{isset($profile_info->billing_company) ? $profile_info->billing_company : 'Non disponibile'}}
        </li>
        <li>
            <strong>CF fatturazione: </strong> {{isset($profile_info->cf) ? $profile_info->cf : 'Non disponibile'}}
        </li>
        <li>
            <strong>PIVA fatturazione: </strong> {{isset($profile_info->vat) ? $profile_info->vat : 'Non disponibile'}}
        </li>
        <li>
            <strong>Indirizzo fatturazione: </strong> {{isset($profile_info->billing_address) ? $profile_info->billing_address : 'Non disponibile'}}
            , {{isset($profile_info->billing_city) ? $profile_info->billing_city : 'Non disponibile'}}
            , {{isset($profile_info->billing_address_zip) ? $profile_info->billing_address_zip : 'Non disponibile'}}
            , {{isset($profile_info->billing_country) ? $profile_info->billing_country : 'Non disponibile'}}
            , {{isset($profile_info->billing_state) ? $profile_info->billing_state : 'Non disponibile'}}
        </li>
    </ul>
    <h5>Spedizione:</h5>
    <ul>
        <li>
            <strong>Nome spedizione: </strong> {{isset($profile_info->shipping_first_name) ? $profile_info->shipping_first_name : 'Non disponibile'}}
        </li>
        <li>
            <strong>Cognome spedizione: </strong> {{isset($profile_info->shipping_last_name) ? $profile_info->shipping_last_name : 'Non disponibile'}}
        </li>
        <li>
            <strong>Ragione sociale spedizione: </strong> {{isset($profile_info->shipping_company) ? $profile_info->shipping_company : 'Non disponibile'}}
        </li>
        <li>
            <strong>PIVA spedizione: </strong> {{isset($profile_info->vat) ? $profile_info->vat : 'Non disponibile'}}
        </li>
        <li>
            <strong>Indirizzo spedizione: </strong> {{isset($profile_info->shipping_address) ? $profile_info->shipping_address : 'Non disponibile'}}
            , {{isset($profile_info->shipping_city) ? $profile_info->shipping_city : 'Non disponibile'}}
            , {{isset($profile_info->shipping_address_zip) ? $profile_info->shipping_address_zip : 'Non disponibile'}}
            , {{isset($profile_info->shipping_country) ? $profile_info->shipping_country : 'Non disponibile'}}
            , {{isset($profile_info->shipping_state) ? $profile_info->shipping_state : 'Non disponibile'}}
        </li>
    </ul>
    @else
      <h3>Dettagli non disponibili.</h3>
    @endif
    <hr/>
    <h3>Dettagli ordine: </h3>
    @foreach($body['order']->getRowOrders() as $order)
        <ul>
            <?php $product = Product::find($order->product_id); ?>
            <li>
                <strong>Nome: </strong>{{$product->decorateLanguage(\L::get_admin())->name}}
            </li>
            <li>
                <strong>Codice: </strong>{{$product->code}}
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
</div>
</body>
</html>