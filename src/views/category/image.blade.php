<div class="row">
    <div class="col-md-12">

        <h3>Immagine associata</h3>
        {{-- image messages --}}
        <?php $message = Session::get('message_img'); ?>
        @if( isset($message) )
        <div class="alert alert-success">{{$message}}</div>
        @endif
        @if($presenter->image)
        <ul class="list-group">
            <li class="list-group-item">
                <span class="pull-left">
                    <img class="media-object img-admin" src="{{$presenter->image['data']}}" alt="{{$presenter->image['alt']}}" />
                </span>
                <span class="clearfix"></span>
            </li>
            @else
            <h5>Non è stata associata un'immagine.</h5>
        </ul>
        @endif
        <hr/>
        <h3>Cambia immagine</h3>
        @if($errors->has('model'))
            <div class="alert alert-danger">{{$errors->first('model')}}</div>
        @endif
        {{Form::open(['action' => 'Palmabit\Catalog\Controllers\CategoryController@postUpdateImage', 'files' => true])}}
        {{Form::hidden("id", $categories->id)}}
        <div class="form-group">
            {{Form::label('image','Seleziona l\'immagine da caricare')}}
        </div>
        <div class="form-group">
            {{Form::file('image')}}
            <span class="text-danger">{{$errors->first('image')}}</span>
        </div>
        <hr>
        {{Form::submit("Salva",["class"=>"btn btn-primary tab-remember"])}}
        {{Form::close()}}

    </div>
</div>

{{-- sezione con js --}}
@section('footer_scripts')
@parent
<script>
    $(".cancella-img").click(function(){
        return confirm("Sei sicuro di volere eliminare l'immagine selezionata?");
    });
</script>
@stop