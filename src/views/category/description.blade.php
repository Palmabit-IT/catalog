@foreach(\L::get_lista() as $lang => $long_name)
<div class="col-md-12">

    <h3><i class="glyphicon glyphicon-globe" style="margin-right: 5px;"></i>{{$long_name}}</h3>
    {{Form::open(['route' => 'category.modifica.descrizione', 'method' => 'post'])}}
    {{Form::hidden('category_id', $categories->id)}}
    {{Form::hidden('id', $presenter->getDescriptionObjectOfLang($lang) ? $presenter->getDescriptionObjectOfLang($lang)->id: null)}}
    {{Form::hidden('lang', $lang)}}
    {{Form::hidden('form_name', 'category_description')}}
    <div class="control-group">
    {{Form::label('slug','Url univoco')}}
    {{Form::text('slug',$presenter->getDescriptionObjectOfLang($lang) ? $presenter->getDescriptionObjectOfLang($lang)->slug: null,['class' => 'form-control'])}}
    <span class="text-danger">{{$errors->first('slug')}}</span>
    </div>
    <div class="control-group">
    {{Form::label('description','Descrizione')}}
    {{Form::text('description',$presenter->getDescriptionObjectOfLang($lang) ? $presenter->getDescriptionObjectOfLang($lang)->description: null,['class' => 'form-control'])}}
    <span class="text-danger">{{$errors->first('description')}}</span>
    </div>
    {{Form::submit('Salva', array("class"=>"btn btn-large btn-warning tab-remember margin-top-10 margin-bottom-10"))}}
    {{Form::close()}}
</div>
@endforeach