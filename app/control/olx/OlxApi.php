<?php

use Adianti\Control\TPage;
use Adianti\Control\TAction;
use Adianti\Validator\TEmailValidator;
use Adianti\Validator\TMaxLengthValidator;
use Adianti\Validator\TMinLengthValidator;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Form\TPassword;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Container\TNotebook;
use Adianti\Widget\Container\TVBox;
use Adianti\Wrapper\BootstrapFormBuilder;

require_once 'request.php';

class OlxApi extends TPage
{
    private $signUp;
    private $signIn;
    private $update;
    private $search;

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

        $vbox = new TVBox;
        $vbox->style = 'width:100%';
        $vbox->add($this->signUp);
        $vbox->add($this->signIn);
        $vbox->add($this->update);
        $vbox->add($this->search);

        // ads

        $notebook = new TNotebook();

        $notebook->appendPage('Categoria', $btnCat);
        $notebook->appendPage('Estado', $btnState);
        $notebook->appendPage('Usuário', $vbox);
        $notebook->appendPage('Ads', '');

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

    public function getUser()
    {
        $data = $this->search->getData();

        $location = "http://localhost/adianti/template/user/me?token={$data->token}";
        $user = request($location, 'GET', [], 'Basic 123');

        new TMessage('info', str_replace(',', '<br>', json_encode($user)));
    }
}
