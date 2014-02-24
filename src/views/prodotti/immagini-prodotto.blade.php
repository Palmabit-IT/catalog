<h3>Immagini associate</h3>
{{-- messaggi immagine --}}
<?php $message = Session::get('message_img'); ?>
@if( isset($message) )
<div class="alert alert-success">{{$message}}</div>
@endif
<ul class="list-group">
    @if($presenter->immagini_all)
    @foreach($presenter->immagini_all as $immagine)
    <li class="list-group-item">
        <span class="pull-left">
            <img class="media-object img-admin" src="{{$immagine['data']}}" alt="{{$immagine['alt']}}" />
        </span>
        {{-- descrizione immagine --}}
        <span class="pull-left">
            Descrizione: {{$immagine['alt']}}
            <br/>
            Immagine in evidenza: {{$immagine['in_evidenza'] ? 'sì' : 'no'}}
        </span>
        {{-- cancellazione --}}
        <a href="{{URL::action('Prodotti\Controllers\ProdottoController@cancellaImmagine',['slug_lingua' => $slug_lingua, 'id' => $immagine['id'] ])}}" class="tab-remember"><span class="glyphicon glyphicon-trash pull-right cancella-img"> Cancella</span></a>
        {{-- set in evidenza --}}
        <br/>
        <a href="{{URL::action('Prodotti\Controllers\ProdottoController@postInEvidenza',['id'=>$immagine['id'], 'prodotto_id' => $prodotto->id, 'slug_lingua' => $slug_lingua])}}" class="tab-remember"><span class="glyphicon glyphicon-ok-sign pull-right"> Evidenza</span></a>
        <span class="clearfix"></span>
    </li>
    @endforeach
    @else
    <h5>Non ho trovato immagini associate.</h5>
    @endif
</ul>
<hr/>
<h3>Aggiungi un'immagine</h3>
@if($errors && ! $errors->isEmpty() )
    @foreach($errors->all() as $error)
        <div class="alert alert-danger">{{$error}}</div>
    @endforeach
@endif
{{Form::open(['action' => 'Prodotti\Controllers\ProdottoController@postImmagine', 'files' => true])}}
{{Form::hidden("slug_lingua", $slug_lingua)}}
{{Form::hidden("prodotto_id", $prodotto->id)}}
<div class="form-group">
{{Form::label('immagine','Seleziona l\'immagine da caricare')}}
</div>
{{FormField::descrizione()}}
<div class="form-group">
{{Form::file('immagine')}}
<span class="text-danger">{{$errors->first('immagine')}}</span>
</div>
<div class="form-group">
{{Form::label('in_evidenza','Immagine in evidenza')}}
{{Form::select('in_evidenza', [1 => "Sì", 0 => 'No'], 0, ["class" => "form-control"])}}
</div>
{{Form::submit("Salva",["class"=>"btn btn-primary tab-remember"])}}
{{Form::close()}}

{{-- sezione con js --}}
@section('footer_scripts')
@parent
<script>
    $(".cancella-img").click(function(){
        return confirm("Sei sicuro di volere eliminare l'immagine selezionata?");
    });
</script>
@stop