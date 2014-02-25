<?php namespace Prodotti\Controllers;

use Illuminate\Support\MessageBag;
use View;
use Input;
use Prodotto;
use Redirect;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Prodotti\Repository\ProdottoRepository;
use Validators\ProdottoValidator;
use Exceptions\PalmabitExceptionsInterface;
use Classes\FormModel;
use Presenters\PresenterProdotti;
use Validators\ImmaginiProdottoValidator;
use Prodotti\Repository\ImmagineRepository;

class ProductsController extends \BaseController {

    /**
     * Prodotto repository
     * @var \Prodotto\ProdottoRepository
     */
    protected $r;

    /**
     * @var \Validators\ProdottoValidator
     */
    protected $v;
    /**
     * FormModel prodotto
     * @var FormModel
     */
    protected $f;
    /**
     * Presenter prodotti
     * @var \Presenters\PresenterProdotti
     */
    protected $p;
    /**
     * FormModel Immagine
     * @var FormModel
     */
    protected $f_img;
    /**
     * Repository Immagine
     * @var \Prodotti\Repository\ImmagineRepository
     */
    protected $r_img;

    public function __construct(ProdottoValidator $v)
    {
        $is_admin = true;
        // prodotto
        $this->r = new ProdottoRepository($is_admin);
        $this->v = $v;
        $this->f = new FormModel($this->v, $this->r);
        // immagini
        $this->r_img = new ImmagineRepository();
        $this->f_img = new FormModel(new ImmaginiProdottoValidator(), $this->r_img);
    }

	public function lista()
	{
        $prodotti = $this->r->all();
        return View::make('admin.prodotti.show')->with(array("prodotti" => $prodotti));
	}

	public function getModifica()
    {
        $slug_lingua = Input::get('slug_lingua');
        try
        {
            $prodotto = $this->r->findBySlugLingua($slug_lingua);
        }
        catch(ModelNotFoundException $e)
        {
            $prodotto = new Prodotto();
        }
        $this->p = new PresenterProdotti($prodotto);

        return View::make('admin.prodotti.modifica')->with(["prodotto" => $prodotto, "slug_lingua" => $slug_lingua, "presenter" => $this->p]);
	}

	public function postModifica()
	{
        $input = Input::all();

        try
        {
            $obj = $this->f->process($input);
        }
        catch(PalmabitExceptionsInterface $e)
        {
            $errors = $this->f->getErrors();
            return Redirect::action("Prodotti\\Controllers\\ProdottoController@getModifica")->withInput()->withErrors($errors);
        }

        return Redirect::action("Prodotti\\Controllers\\ProdottoController@getModifica",["slug_lingua" => $obj->slug_lingua])->with(array("message"=>"Prodotto modificato con successo."));
	}

	public function cancella()
	{
        $input = Input::all();

        try
        {
            $this->f->delete($input);
        }
        catch(PalmabitExceptionsInterface $e)
        {
            $errors = $this->f->getErrors();
            return Redirect::action("Prodotti\\Controllers\\ProdottoController@lista")->withErrors($errors);
        }

        return Redirect::action("Prodotti\\Controllers\\ProdottoController@lista")->with(array("message"=>"Prodotto eliminato con successo."));
	}

    public function postCategoria()
    {
        $prodotto_id = Input::get('prodotto_id');
        $slug_lingua= Input::get('slug_lingua');
        $categoria_id = Input::get('categoria');

        try
        {
            $this->r->associaCategoria($prodotto_id, $categoria_id);
        }
        catch(ModelNotFoundException $e)
        {
            return Redirect::action("Prodotti\\Controllers\\ProdottoController@getModifica",["slug_lingua" => $slug_lingua])->withErrors(new MessageBag("Prodotto non trovato."));
        }

        return Redirect::action("Prodotti\\Controllers\\ProdottoController@getModifica",["slug_lingua" => $slug_lingua])->with(["message_cat"=>"Categoria associata con successo."]);
    }

    public function postImmagine()
    {
        $input = Input::all();
        $slug_lingua= Input::get('slug_lingua');

        try
        {
            $this->f_img->process($input);
        }
        catch(PalmabitExceptionsInterface $e)
        {
            $errors = $this->f_img->getErrors();
            return Redirect::action("Prodotti\\Controllers\\ProdottoController@getModifica", ["slug_lingua" => $slug_lingua])->withInput()->withErrors($errors);
        }

        return Redirect::action("Prodotti\\Controllers\\ProdottoController@getModifica",["slug_lingua" => $slug_lingua])->with(array("message_img"=>"Immagine caricata con successo."));
    }

    public function cancellaImmagine()
    {
        $input = Input::all();
        $slug_lingua= Input::get('slug_lingua');

        try
        {
            $this->f_img->delete($input);
        }
        catch(PalmabitExceptionsInterface $e)
        {
            $errors = $this->f_img->getErrors();
            return Redirect::action("Prodotti\\Controllers\\ProdottoController@getModifica", ["slug_lingua" => $slug_lingua])->withInput()->withErrors($errors);
        }

        return Redirect::action("Prodotti\\Controllers\\ProdottoController@getModifica",["slug_lingua" => $slug_lingua])->with(array("message_img"=>"Immagine eliminata con successo."));
    }

    public function postInEvidenza($id, $prodotto_id)
    {
        $slug_lingua= Input::get('slug_lingua');

        try
        {
            $this->r_img->cambiaEvidenza($id, $prodotto_id);
        }
        catch(PalmabitExceptionsInterface $e)
        {
            return Redirect::action("Prodotti\\Controllers\\ProdottoController@getModifica", ["slug_lingua" => $slug_lingua])->withErrors(new MessageBag(["model" => $e->getMessage()]));
        }

        return Redirect::action("Prodotti\\Controllers\\ProdottoController@getModifica",["slug_lingua" => $slug_lingua])->with(array("message_img"=>"Immagine in evidenza impostata con successo."));
    }

    public function postModificaOrdine()
    {
        $input = Input::all();
        $validator = new ProdottoFormOrdineValidator;
        $form_model = new FormModel($validator, $this->r);

        try
        {
            $obj = $form_model->process($input);
        }
        catch(PalmabitExceptionsInterface $e)
        {
            $errors = $form_model->getErrors();
            return Redirect::action("Prodotti\\Controllers\\ProdottoController@lista")->withInput()->withErrors($errors);
        }

        return Redirect::action("Prodotti\\Controllers\\ProdottoController@lista")->with(array("message"=>"Ordine modificato con successo."));
    }


    public function associaAccessorio()
    {
        $prodotto_id = Input::get('prodotto_id');
        $accessorio_id = Input::get('accessorio_id');
        $slug_lingua= Input::get('slug_lingua');

        try
        {
            $this->r->associaAccessorio($prodotto_id, $accessorio_id);
        }
        catch(ModelNotFoundException $e)
        {
            return Redirect::action("Prodotti\\Controllers\\ProdottoController@getModifica", ["slug_lingua" => $slug_lingua])->withErrors(new MessageBag(["model" => "Accessorio non trovato."]));
        }

        return Redirect::action("Prodotti\\Controllers\\ProdottoController@getModifica",["slug_lingua" => $slug_lingua])->with(array("message_acc"=>"Accessorio associato con successo."));
    }

    public function deassociaAccessorio()
    {
        $prodotto_id = Input::get('prodotto_id');
        $accessorio_id = Input::get('accessorio_id');
        $slug_lingua= Input::get('slug_lingua');

        try
        {
            $this->r->deassociaAccessorio($prodotto_id, $accessorio_id);
        }catch(ModelNotFounxException $e)
        {
            return Redirect::action("Prodotti\\Controllers\\ProdottoController@getModifica", ["slug_lingua" => $slug_lingua])->withErrors(new MessageBag(["model" => "Accessorio non trovato."]));
        }

        return Redirect::action("Prodotti\\Controllers\\ProdottoController@getModifica",["slug_lingua" => $slug_lingua])->with(array("message_acc"=>"Accessorio deassociato con successo."));
    }
}
