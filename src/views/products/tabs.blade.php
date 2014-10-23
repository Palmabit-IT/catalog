<ul class="nav nav-tabs" id="tab-prodotto">
    <li class="active tab-link" id="link-tab-base"><a href="#tab-generale" {{$presenter->get_toggle()}} >{{L::t('Generale')}}</a></li>
    <li class="tab-link" id="link-tab-categoria"><a href="#tab-categoria" {{$presenter->get_toggle()}} >{{L::t('Categoria')}}</a></li>
    <li id="link-tab-immagini" class="tab-link"><a href="#tab-immagini" {{$presenter->get_toggle()}} >{{L::t('Immagini')}}</a></li>
    <li id="link-tab-accessories" class="tab-link"><a href="#tab-accessories" {{$presenter->get_toggle()}} >{{L::t('Accessori')}}</a></li>
</ul>
<br/>