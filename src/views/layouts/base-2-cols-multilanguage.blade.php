@extends('authentication::layouts.base')

@section('head_css')
    {{ HTML::style('packages/palmabit/catalog/css/catalog.css') }}
@stop

@section('container')
<div class="col-md-2 nav bs-sidenav">
    @include('authentication::layouts.sidebar')
</div>
<div class="col-md-10">
    {{-- select languages --}}
    {{get_form_select_lang()}}
    <br/>
    @yield('content')
</div>
@stop

@section('footer_scripts')
<script>
    $( document ).ready(function() {
        $("#select-lang").change(function(){
            $("#form-select-lang").submit()
        });
    });
</script>
@stop