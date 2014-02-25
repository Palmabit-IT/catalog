{{-- messaggi accessori --}}
<?php $message = Session::get('message_acc'); ?>
@if( isset($message) )
<div class="alert alert-success">{{$message}}</div>
@endif
{{-- messagi di errore --}}
@if($errors && ! $errors->isEmpty() )
@foreach($errors->all() as $error)
<div class="alert alert-danger">{{$error}}</div>
@endforeach
@endif

{{-- accessori prodotto --}}
<h3>Accessori già associati al prodotto</h3>
@if($presenter->accessori)
    <ul class="list-group">
    @foreach($presenter->accessori as $accessorio)
        <li class="list-group-item"><span class="glyphicon glyphicon-cog blue"></span>
            {{$accessorio["descrizione"]}}
            <a href="{{URL::action('Prodotti\Controllers\ProdottoController@deassociaAccessorio',['accessorio_id'=>$accessorio['id'], 'prodotto_id' => $prodotto->id, 'slug_lingua'=>$slug_lingua])}}" class="cancella-acc pull-right tab-remember"><span class="glyphicon glyphicon-trash"> cancella</span></a></li>
        </li>
    @endforeach
    </ul>
@else
    <h5>Non ci sono accessori associati al prodotto.</h5>
@endif

{{-- accessori categoria --}}
<h3>Accessori associati alla categoria</h3>
@if($presenter->accessori_categoria)
    <ul class="list-group">
    @foreach($presenter->accessori_categoria as $accessorio)
        <li class="list-group-item"><span class="glyphicon glyphicon-cog blue"></span>
            {{$accessorio["descrizione"]}}
        </li>
    @endforeach
    </ul>
@else
    <h5>Non ci sono accessori associati alla categoria.</h5>
@endif

{{-- associazione accessori al prodotto--}}
<h3>Associazione nuovo accessorio:</h3>
{{Form::open(["action"=>"Prodotti\Controllers\ProdottoController@associaAccessorio", "method" => "post"])}}
<div class="form-group">
    {{Form::select('accessorio_id', get_select_accessori_arr(), '', ["class" => "form-control"])}}
</div>
{{Form::hidden("slug_lingua", $slug_lingua)}}
{{Form::hidden("prodotto_id", $prodotto->id)}}
<div class="form-group">
    {{Form::submit('Associa', ["class" => "btn btn-primary tab-remember"])}}
</div>
{{Form::close()}}

@section('footer_scripts')
@parent
<script>
    $(".cancella-acc").click(function(){
        return confirm("Sei sicuro di volere deassociare l'accessorio selezionato?");
    });
</script>
@stop