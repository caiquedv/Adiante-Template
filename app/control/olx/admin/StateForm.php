<?php

use Adianti\Control\TPage;
use Adianti\Control\TAction;
use Adianti\Registry\TSession;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Wrapper\BootstrapFormBuilder;

class StateForm extends TPage
{
    private $form;

    use Adianti\Base\AdiantiStandardFormTrait;

    public function __construct()
    {
        parent::__construct();

        $this->setDatabase('olx');
        $this->setActiveRecord('StatesOlxApi');

        $this->form = new BootstrapFormBuilder('form_states');
        $this->form->setFormTitle('Estados');

        $this->form->setClientValidation(true);

        $id  = new TEntry('id');
        $name = new TEntry('name');

        $id->setEditable(FALSE);

        $this->form->addFields([new TLabel('Cód.')], [$id]);
        $this->form->addFields([new TLabel('Nome', 'red')], [$name]);

        $name->addValidation('Nome', new TRequiredValidator);

        $this->form->addHeaderAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green'); 

        $this->form->addHeaderActionLink('Limpar', new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->form->addHeaderActionLink('Voltar', new TAction(['StateList', 'clear']), 'fa:arrow-left black');

        parent::add($this->form);
    }
}
