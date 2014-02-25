@extends('authentication::layouts.base-2cols')

@section('title')
{{$app_name}} Admin area: insersci prodotto
@stop

@section('content')

    {{-- bootstrap 3 tabs --}}
    @include('authentication::products.tabs')
    <div class="tab-content">
    <div class="tab-pane fade in active" id="tab-generale">
        @include('catalog::products.general')
    </div>
    <div class="tab-pane fade" id="tab-categoria">
        @include('catalog::products.category')
    </div>
    <div class="tab-pane fade" id="tab-immagini">
        @include('catalog::products.image')
    </div>
    </div>
@stop

@section('footer_scripts')
@parent
    {{ HTML::script('packages/catalog/js/salva-tab.js') }}
@stop