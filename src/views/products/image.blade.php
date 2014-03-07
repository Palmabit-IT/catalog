<h3>Immagini associate</h3>
{{-- image messages --}}
<?php $message = Session::get('message_img'); ?>
@if( isset($message) )
<div class="alert alert-success">{{$message}}</div>
@endif
<ul class="list-group">
    @if($presenter->images_all)
    @foreach($presenter->images_all as $image)
    <li class="list-group-item">
        <span class="pull-left">
            <img class="media-object img-admin" src="{{$image['data']}}" alt="{{$image['alt']}}" />
        </span>
        {{-- descrizione immagine --}}
        <span class="pull-left">
            Descrizione: {{$image['alt']}}
            <br/>
            Immagine in evidenza: {{$image['featured'] ? 'sì' : 'no'}}
        </span>
        {{-- cancellazione --}}
        <a href="{{URL::action('Palmabit\Catalog\Controllers\ProductsController@deleteImage',['slug_lang' => $slug_lang, 'id' => $image['id'] ])}}" class="tab-remember"><span class="glyphicon glyphicon-trash pull-right cancella-img"> Cancella</span></a>
        {{-- set in evidenza --}}
        <br/>
        <a href="{{URL::action('Palmabit\Catalog\Controllers\ProductsController@postFeatured',['id'=>$image['id'], 'product_id' => $product->id, 'slug_lang' => $slug_lang])}}" class="tab-remember"><span class="glyphicon glyphicon-ok-sign pull-right"> Evidenza</span></a>
        <span class="clearfix"></span>
    </li>
    @endforeach
    @else
    <h5>Non ho trovato immagini as esociate.</h5>
    @endif
</ul>
<hr/>
<h3>Aggiungi un'immagine</h3>
@if($errors && ! $errors->isEmpty() )
    @foreach($errors->all() as $error)
        <div class="alert alert-danger">{{$error}}</div>
    @endforeach
@endif
{{Form::open(['action' => 'Palmabit\Catalog\Controllers\ProductsController@postImage', 'files' => true])}}
{{Form::hidden("slug_lang", $slug_lang)}}
{{Form::hidden("product_id", $product->id)}}
<div class="form-group">
{{Form::label('image','Seleziona l\'immagine da caricare')}}
</div>
{{FormField::description(["label" => "descrizione: *", 'type' => 'text'])}}
<div class="form-group">
{{Form::file('image')}}
<span class="text-danger">{{$errors->first('image')}}</span>
</div>
<div class="form-group">
{{Form::label('featured','Immagine in evidenza')}}
{{Form::select('featured', [1 => "Sì", 0 => 'No'], 0, ["class" => "form-control"])}}
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