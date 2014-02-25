aggiornaTab();

function aggiornaTab()
{
    if( (href = localStorage.getItem("ultimo_tab")) != null)
    {
        selettore = '#tab-prodotto a[href="' + href + '"]';
        $(selettore).tab('show');
        localStorage.removeItem("ultimo_tab");
    }
}

$('.tab-remember').on('click', function(){
    href = $('li.tab-link.active').children().attr('href');
    localStorage.setItem("ultimo_tab", href);
});