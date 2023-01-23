<?php

use Adianti\Control\TPage;
use Adianti\Control\TAction;
use Adianti\Registry\TSession;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Form\TPassword;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Container\TNotebook;
use Adianti\Wrapper\BootstrapFormBuilder;

require_once 'request.php';

class OlxApi extends TPage
{
    private $form;


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

        //signup
        $options = ['1' => 'SP', '2' => 'RJ', '3' => 'MG'];

        $this->form = new BootstrapFormBuilder;
        $token = new TEntry('token');
        $name = new TEntry('name');
        $email = new TEntry('email');
        $state = new TCombo('state');
        $password = new TPassword('password');

        $state->addItems($options);

        $this->form->addFields([new TLabel('Token')], [$token]);
        $this->form->addFields([new TLabel('Nome')], [$name]);
        $this->form->addFields([new TLabel('E-mail')], [$email]);
        $this->form->addFields([new TLabel('Estado')], [$state]);
        $this->form->addFields([new TLabel('Senha')], [$password]);

        $this->form->setData(TSession::getValue(__CLASS__ . '_filter_data'));

        $this->form->addAction('Criar', new TAction([$this, 'postUser']), 'fa:paper-plane blue');
        $this->form->addAction('Atualizar', new TAction([$this, 'putUser']), 'fa:wrench blue');
        $this->form->addAction('Buscar', new TAction([$this, 'getUser']), 'fa:search blue');

        $notebook = new TNotebook();

        $notebook->appendPage('Categoria', $btnCat);
        $notebook->appendPage('Estado', $btnState);
        $notebook->appendPage('Usuário', $this->form);
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

    public function postUser()
    {
        $data = $this->form->getData();

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

    public function putUser()
    {
        $data = $this->form->getData();

        $location = "http://localhost/adianti/template/user/me?";
        $user = request($location, 'PUT', get_object_vars($data), 'Basic 123');

        new TMessage('info', json_encode($user));
    }

    public function getUser()
    {
        $data = $this->form->getData();

        $location = "http://localhost/adianti/template/user/me?token={$data->token}";
        $user = request($location, 'GET', [], 'Basic 123');

        new TMessage('info', str_replace(',', '<br>', json_encode($user)));
    }
}
