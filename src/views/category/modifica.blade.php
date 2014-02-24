@extends('admin.layouts.base-2cols')

@section('title')
{{$nome_sito}} Admin area: insersci categorie
@stop

@section('content')
    {{-- bootstrap 3 tabs --}}
    @include('admin.category.tabs')
    <div class="tab-content">
        <div class="tab-pane fade in active" id="tab-generale">
            @include('admin.category.generale')
        </div>
        <div class="tab-pane fade" id="tab-accessori">
            @include('admin.category.accessori')
        </div>
    </div>
@stop

@section('footer_scripts')
@parent
    {{ HTML::script('admin/js/salva-tab.js') }}
@stop