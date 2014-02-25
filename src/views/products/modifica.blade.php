@extends('admin.layouts.base-2cols')

@section('title')
{{$nome_sito}} Admin area: insersci prodotto
@stop


@section('content')

    {{-- bootstrap 3 tabs --}}
    @include('admin.prodotti.tabs')
    <div class="tab-content">
    <div class="tab-pane fade in active" id="tab-generale">
        @include('admin.prodotti.prodotto-generale')
    </div>
    <div class="tab-pane fade" id="tab-categoria">
        @include('admin.prodotti.categoria-prodotto')
    </div>
    <div class="tab-pane fade" id="tab-tags">
        @include('admin.prodotti.tags-prodotto')
    </div>
    <div class="tab-pane fade" id="tab-accessori">
        @include('admin.prodotti.accessori-prodotto')
    </div>
    <div class="tab-pane fade" id="tab-immagini">
        @include('admin.prodotti.immagini-prodotto')
    </div>
    </div>
@stop

@section('footer_scripts')
@parent
    {{ HTML::script('admin/js/salva-tab.js') }}
@stop