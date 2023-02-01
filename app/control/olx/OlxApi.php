<?php

use Adianti\Control\TPage;
use Adianti\Control\TAction;
use Adianti\Validator\TEmailValidator;
use Adianti\Validator\TMaxLengthValidator;
use Adianti\Validator\TMinLengthValidator;
use Adianti\Validator\TMinValueValidator;
use Adianti\Validator\TNumericValidator;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Form\TPassword;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Container\TNotebook;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Form\TRadioGroup;
use Adianti\Wrapper\BootstrapFormBuilder;

require_once 'request.php';

class OlxApi extends TPage
{
    private $signUp;
    private $signIn;
    private $update;
    private $search;

    private $addAd;
    private $getAd;
    private $updtAd;

    public function __construct()
    {
        parent::__construct();
        //categories
        $btnCat = new TButton('categoty_btn');
        $btnCat->setLabel('Buscar');
        $btnCat->setImage('fa:search blue');
        $btnCat->addFunction("{ __adianti_load_page('index.php?class=OlxApi&method=getCategories'); }");

        //states
        $btnState = new TButton('state_btn');
        $btnState->setLabel('Buscar');
        $btnState->setImage('fa:search blue');
        $btnState->addFunction("{ __adianti_load_page('index.php?class=OlxApi&method=getStates'); }");

        //user forms
        $this->signUp = new BootstrapFormBuilder('signup');
        $this->signIn = new BootstrapFormBuilder('signin');
        $this->update = new BootstrapFormBuilder('update');
        $this->search = new BootstrapFormBuilder('search');

        // html validate
        $this->signUp->setClientValidation(true);
        $this->signIn->setClientValidation(true);
        $this->update->setClientValidation(true);
        $this->search->setClientValidation(true);

        // entries && validators
        // // signup
        $nameUp = new TEntry('name');
        $emailUp = new TEntry('email');
        $stateUp = new TCombo('state');
        $passwordUp = new TPassword('password');

        $nameUp->addValidation('Name up', new TRequiredValidator);
        $emailUp->addValidation('Email up', new TEmailValidator);
        $emailUp->addValidation('Email up', new TRequiredValidator);
        $stateUp->addValidation('State up', new TRequiredValidator);
        $passwordUp->addValidation('Pass up', new TMinLengthValidator, [4]);
        $passwordUp->addValidation('Pass up', new TRequiredValidator);

        // // signin
        $emailIn = new TEntry('email');
        $passwordIn = new TPassword('password');

        $emailIn->addValidation('Email in', new TEmailValidator);
        $emailIn->addValidation('Email in', new TRequiredValidator);
        $passwordIn->addValidation('Pass in', new TMinLengthValidator, [4]);
        $passwordIn->addValidation('Pass in', new TRequiredValidator);

        // // update
        $tokenUpdt = new TEntry('token');
        $nameUpdt = new TEntry('name');
        $emailUpdt = new TEntry('email');
        $stateUpdt = new TCombo('state');
        $passwordUpdt = new TPassword('password');

        $tokenUpdt->addValidation('Token Updt', new TRequiredValidator);
        $tokenUpdt->addValidation('Token Updt', new TMaxLengthValidator, [32]);
        $tokenUpdt->addValidation('Token Updt', new TMinLengthValidator, [32]);
        $nameUpdt->addValidation('Name Updt', new TMinLengthValidator, [4]);
        $emailUpdt->addValidation('Email Updt', new TEmailValidator);
        $passwordUpdt->addValidation('Pass up', new TMinLengthValidator, [4]);

        // // search
        $tokenSearch = new TEntry('token');

        $tokenSearch->addValidation('Token Search', new TRequiredValidator);
        $tokenSearch->addValidation('Token Search', new TMaxLengthValidator, [32]);
        $tokenSearch->addValidation('Token Search', new TMinLengthValidator, [32]);

        $options = ['1' => 'SP', '2' => 'RJ', '3' => 'MG'];
        $stateUp->addItems($options);
        $stateUpdt->addItems($options);

        // fields
        // // signUp
        $this->signUp->addFields([new TLabel('Nome')], [$nameUp]);
        $this->signUp->addFields([new TLabel('E-mail')], [$emailUp]);
        $this->signUp->addFields([new TLabel('Estado')], [$stateUp]);
        $this->signUp->addFields([new TLabel('Senha')], [$passwordUp]);

        // // signIn
        $this->signIn->addFields([new TLabel('E-mail')], [$emailIn]);
        $this->signIn->addFields([new TLabel('Senha')], [$passwordIn]);

        // // update
        $this->update->addFields([new TLabel('Token')], [$tokenUpdt]);
        $this->update->addFields([new TLabel('Nome')], [$nameUpdt]);
        $this->update->addFields([new TLabel('E-mail')], [$emailUpdt]);
        $this->update->addFields([new TLabel('Estado')], [$stateUpdt]);
        $this->update->addFields([new TLabel('Senha')], [$passwordUpdt]);

        // // search
        $this->search->addFields([new TLabel('Token')], [$tokenSearch]);

        //actions
        $this->signUp->addAction('Cadastrar', new TAction([$this, 'signUp']), 'fa:paper-plane blue');
        $this->signIn->addAction('Login', new TAction([$this, 'signIn']), 'fa:paper-plane blue');
        $this->update->addAction('Atualizar', new TAction([$this, 'putUser']), 'fa:wrench blue');
        $this->search->addAction('Buscar', new TAction([$this, 'getUser']), 'fa:search blue'); 

        $userVbox = new TVBox;
        $userVbox->style = 'width:100%';
        $userVbox->add($this->signUp);
        $userVbox->add($this->signIn);
        $userVbox->add($this->update);
        $userVbox->add($this->search);

        // ads
        // // ads form
        $this->addAd = new BootstrapFormBuilder('addad');
        $this->getAd = new BootstrapFormBuilder('getad');
        $this->updtAd = new BootstrapFormBuilder('updatead');

        $this->addAd->setClientValidation(true);
        $this->updtAd->setClientValidation(true);

        // entries && validators
        // // post an ad
        $token_add = new TEntry('token');
        $status_add = new TRadioGroup('status');
        $title_add = new TEntry('title');
        $category_add = new TCombo('category');
        $price_add = new TEntry('price'); // colocar mask
        $priceNeg = new TRadioGroup('price_negotiable');
        $description = new TEntry('description');

        $catOptions = ['1' => 'Esportes', '2' => 'Eletônicos', '3' => 'Roupas', '4' => 'Carros', '5' => 'Bebês'];
        $statusOpt = [true => 'Ativo', false => 'Inativo'];

        $category_add->addItems($catOptions);
        $status_add->addItems($statusOpt);
        $status_add->setLayout('horizontal');

        $priceNeg->addItems($statusOpt);
        $priceNeg->setLayout('horizontal');

        $token_add->addValidation('Token Add', new TRequiredValidator);
        $token_add->addValidation('Token Add', new TMaxLengthValidator, [32]);
        $token_add->addValidation('Token Add', new TMinLengthValidator, [32]);
        $title_add->addValidation('Title Add', new TRequiredValidator);
        $title_add->addValidation('Title Add', new TMaxLengthValidator, [50]);
        $title_add->addValidation('Title Add', new TMinLengthValidator, [4]);
        $category_add->addValidation('Category Add', new TRequiredValidator);
        $price_add->addValidation('Price Add', new TRequiredValidator);
        $price_add->addValidation('Price Add', new TNumericValidator);
        $price_add->setNumericMask(2, ',', '.', true); // ? true(replaceonpost) ñ funciona
        $price_add->addValidation('Price Add', new TMinValueValidator, [1]); // fazer tratamento de exceções p funcionar
        $description->addValidation('Description Add', new TRequiredValidator);
        $description->addValidation('Description Add', new TMaxLengthValidator, [100]);
        $description->addValidation('Description Add', new TMinLengthValidator, [4]);

        // fields
        // // post an ad
        $this->addAd->addFields([new TLabel('Token')], [$token_add]);
        $this->addAd->addFields([new TLabel('Status')], [$status_add]);
        $this->addAd->addFields([new TLabel('Título')], [$title_add]);
        $this->addAd->addFields([new TLabel('Categoria')], [$category_add]);
        $this->addAd->addFields([new TLabel('Preço')], [$price_add]);
        $this->addAd->addFields([new TLabel('Preço Negociavel')], [$priceNeg]);
        $this->addAd->addFields([new TLabel('Descrição')], [$description]);

        //get an ad
        $ad_id = new TEntry('id');
        $this->getAd->addFields([new TLabel('Id')], [$ad_id]);

        // update an ad
        $token_updt = new TEntry('token');
        $id_updt = new TEntry('id');
        $status_updt = new TRadioGroup('status');
        $title_updt = new TEntry('title');
        $category_updt = new TCombo('category');
        $price_updt = new TEntry('price'); // colocar mask
        $priceNeg_updt = new TRadioGroup('price_negotiable');
        $desc_updt = new TEntry('description');

        // $catOptions = ['1' => 'Esportes', '2' => 'Eletônicos', '3' => 'Roupas', '4' => 'Carros', '5' => 'Bebês'];

        $category_updt->addItems($catOptions);
        $status_updt->addItems($statusOpt);
        $status_updt->setLayout('horizontal');
        $priceNeg_updt->addItems($statusOpt);
        $priceNeg_updt->setLayout('horizontal');

        $token_updt->addValidation('Token Add', new TRequiredValidator);
        $token_updt->addValidation('Token Add', new TMaxLengthValidator, [32]);
        $token_updt->addValidation('Token Add', new TMinLengthValidator, [32]);

        $id_updt->addValidation('Id Add', new TRequiredValidator);

        $this->updtAd->addFields([new TLabel('Token')], [$token_updt]);
        $this->updtAd->addFields([new TLabel('Id')], [$id_updt]);
        $this->updtAd->addFields([new TLabel('Status')], [$status_updt]);
        $this->updtAd->addFields([new TLabel('Título')], [$title_updt]);
        $this->updtAd->addFields([new TLabel('Categoria')], [$category_updt]);
        $this->updtAd->addFields([new TLabel('Preço')], [$price_updt]);
        $this->updtAd->addFields([new TLabel('Preço Negociavel')], [$priceNeg_updt]);
        $this->updtAd->addFields([new TLabel('Descrição')], [$desc_updt]);

        // actions
        $this->addAd->addAction('Postar', new TAction([$this, 'postAdd']), 'fa:paper-plane blue');
        $this->getAd->addAction('Buscar', new TAction([$this, 'listAd']), 'fa:paper-plane blue');
        $this->updtAd->addAction('Atualizar', new TAction([$this, 'updateAd']), 'fa:paper-plane blue');

        $adsVbox = new TVBox;
        $adsVbox->style = 'width:100%';
        $adsVbox->add($this->addAd);
        $adsVbox->add($this->getAd);
        $adsVbox->add($this->updtAd);

        $notebook = new TNotebook();

        $notebook->appendPage('Categoria', $btnCat);
        $notebook->appendPage('Estado', $btnState);
        $notebook->appendPage('Usuário', $userVbox);
        $notebook->appendPage('Ads', $adsVbox);

        parent::add($notebook);
    }

    public function getCategories()
    {
        $body['order'] = 'id';
        $body['direction'] = 'asc';
        $location = 'http://localhost/adianti/template/categories';
        $categories = request($location, 'GET', [], 'Basic 123');

        new TMessage('info', str_replace(',', '<br>', json_encode($categories)));
    }

    public function getStates()
    {
        $body['order'] = 'id';
        $body['direction'] = 'asc';
        $location = 'http://localhost/adianti/template/states';
        $states = request($location, 'GET', [], 'Basic 123');

        new TMessage('info', str_replace(',', '<br>', json_encode($states)));
    }

    public function signUp()
    {
        $data = $this->signUp->getData();

        $body = [
            'name' => $data->name,
            'email' => $data->email,
            'state' => $data->state,
            'password' => $data->password
        ];
        $location = 'http://localhost/adianti/template/user/signup';
        $data = request($location, 'POST', $body, 'Basic 123');

        new TMessage('info', str_replace(',', '<br>', json_encode($data)));
    }
    public function signIn()
    {
        $data = $this->signIn->getData();

        $body = [
            'email' => $data->email,
            'password' => $data->password
        ];
        $location = 'http://localhost/adianti/template/user/signin';
        $data = request($location, 'POST', $body, 'Basic 123');

        new TMessage('info', str_replace(',', '<br>', json_encode($data)));
    }

    public function putUser()
    {
        $data = $this->update->getData();

        $location = "http://localhost/adianti/template/user/me?";
        $user = request($location, 'PUT', get_object_vars($data), 'Basic 123');

        new TMessage('info', json_encode($user));
    }

    public function getUser($param)
    {
        $data = $this->search->getData() ?? $param;

        $location = "http://localhost/adianti/template/user/me?token={$data->token}";
        $user = request($location, 'GET', [], 'Basic 123');

        new TMessage('info', str_replace(',', '<br>', json_encode($user)));
        // return $user;
    }

    public function postAdd($param)
    {
        $location = "http://localhost/adianti/template/user/me?token={$param['token']}";
        $user = request($location, 'GET', [], 'Basic 123');

        if ($user) {
            $body = [
                'status' => $param['status'],
                'user_id' => $user[0]->id,
                'state' => $user[0]->state,
                'title' => $param['title'],
                'category' => $param['category'],
                'price' => str_replace(['.', ','], ['', '.'], $param['price']),
                'price_negotiable' => $param['price_negotiable'],
                'description' => $param['description'],
                'views' => 0
            ];

            $location = 'http://localhost/adianti/template/ad/add';
            $ad = request($location, 'POST', $body, 'Basic 123');
        }

        if (isset($ad) && $ad) {
            new TMessage('info', "Anúncio Postado! Id: {$ad->id}");
        } else {
            new TMessage('error', 'Não foi possível postar o anúncio!');
        }
    }

    public function listAd($param)
    {
        $body = [];

        if (isset($param['id']) && $param['id']) $body['filters'] = [['id', '=', $param['id']]];

        $location = "http://localhost/adianti/template/ad/list/{$param['id']}";
        $ads = request($location, 'GET', $body, 'Basic 123');

        new TMessage('info', str_replace(',', '<br>', json_encode($ads)));
    }

    public function updateAd($param)
    {
        $location = "http://localhost/adianti/template/user/me?token={$param['token']}";
        $user = request($location, 'GET', [], 'Basic 123');

        if ($user) {
            $location = "http://localhost/adianti/template/ad/{$param['id']}";
            $ad = request($location, 'GET', [], 'Basic 123');

            if ($ad->user_id === $user[0]->id) {
                $location = "http://localhost/adianti/template/ad/{$param['id']}";
                $body = [];

                if (isset($param['status']) && $param['status'] != '')  $body['status'] = $param['status'];
                
                if (isset($param['title']) && $param['title'] != '')  $body['title'] = $param['title'];

                if (isset($param['category']) && $param['category'] != '')  $body['category'] = $param['category'];

                if (isset($param['price']) && $param['price'] != '')  $body['price'] = $param['price'];

                if (isset($param['price_negotiable']) && $param['price_negotiable'] != '')  $body['price_negotiable'] = $param['price_negotiable'];

                if (isset($param['description']) && $param['description'] != '')  $body['description'] = $param['description'];

                $response = null;
                if ($body) $response = request($location, 'PUT', $body, 'Basic 123');
            }
        }
        new TMessage('info', str_replace(',', '<br>', json_encode($response)));
    }
}
