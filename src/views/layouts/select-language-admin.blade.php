{{Form::select('language',$languages,L::get_admin() , ['class' => 'form-control', 'id' => 'change-language-admin'])}}
@section('footer_scripts')
@parent
<script>
    "use strict";
    $(document).ready(function(){
        // change language on select change
        $("#change-language-admin").on('change',function(event){
            var new_lang = $(this).val();

            $.ajax({
                url: '/lang/changeAdmin/'+new_lang,
                type: "POST"
            }).done(function(){
                window.location.reload();
            });
        });
    });
</script>

@stop