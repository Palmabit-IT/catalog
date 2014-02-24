{{-- messaggi tags --}}
<?php $message = Session::get('message_tag'); ?>
@if( isset($message) )
    <div class="alert alert-success">{{$message}}</div>
@endif
{{-- messaggi di errore --}}
@if($errors && ! $errors->isEmpty() )
    @foreach($errors->all() as $error)
        <div class="alert alert-danger">{{$error}}</div>
    @endforeach
@endif
{{-- lista tag già associati --}}
<h3>Tags associati al prodotto:</h3>
@if($presenter->tags)
    <ul class="list-group">
    @foreach($presenter->tags as $tag)
        <li class="list-group-item"><span class="glyphicon glyphicon-tag blue"></span> {{$tag["descrizione"]}}
        <a href="{{URL::action('Prodotti\Controllers\TagsController@cancellaTag',['id'=>$tag['id'], 'slug_lingua'=>$slug_lingua])}}" class="cancella-tag pull-right tab-remember"><span class="glyphicon glyphicon-trash"> cancella</span></a></li>
    @endforeach
    </ul>
@else
    <h5>Nessun tag associato al prodotto.</h5>
@endif

{{-- associazione nuovi tags --}}
<h3>Associazione nuovo tag:</h3>
@if($presenter->tags_select)
    {{Form::open(["action" => 'Prodotti\Controllers\TagsController@creaTag', "method" => "post"])}}
    <div class="form-group">
        {{Form::select('descrizione', $presenter->tags_select, '', ["class" => "form-control"])}}
   </div>
   {{Form::hidden("slug_lingua", $slug_lingua)}}
   {{Form::hidden("prodotto_id", $prodotto->id)}}
   <div class="form-group">
        {{Form::submit('Associa', ["class" => "btn btn-primary tab-remember"])}}
   </div>
   {{Form::close()}}
@else
    <h5>Non ci sono tag da associare: creane uno nuovo</h5>
@endif
{{-- creazione nuovi tags --}}
<h3>Creazione tag:</h3>
{{Form::open(["action" => 'Prodotti\Controllers\TagsController@creaTag', "method" => "post"])}}
{{FormField::descrizione()}}
{{Form::hidden("slug_lingua", $slug_lingua)}}
{{Form::submit('Crea', ["class" => "btn btn-primary tab-remember"])}}
{{Form::close()}}

@section('footer_scripts')
@parent
<script>
    $(".cancella-tag").click(function(){
        return confirm("Sei sicuro di volere cancellare il tag selezionato?");
    });
</script>
@stop