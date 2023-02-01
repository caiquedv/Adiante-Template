<?php

use Adianti\Control\TPage;
use Adianti\Control\TAction;
use Adianti\Registry\TSession;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Wrapper\BootstrapFormBuilder;

class CategoryForm extends TPage
{
    private $form;

    use Adianti\Base\AdiantiStandardFormTrait;

    public function __construct()
    {
        parent::__construct();

        $this->setDatabase('olx');
        $this->setActiveRecord('CategoriesOlxApi');

        $this->form = new BootstrapFormBuilder('form_categories');
        $this->form->setFormTitle('Categorias');

        $this->form->setClientValidation(true);

        $id  = new TEntry('id');
        $name = new TEntry('name');
        $slug = new TEntry('slug');
        $id->setEditable(FALSE);

        $this->form->addFields([new TLabel('Cód.')], [$id]);
        $this->form->addFields([new TLabel('Nome', 'red')], [$name]);
        $this->form->addFields([new TLabel('Slug', 'red')], [$slug]);

        // $this->form->setData(__CLASS__);

        $name->addValidation('Nome', new TRequiredValidator);
        $slug->addValidation('Slug', new TRequiredValidator);

        $this->form->addHeaderAction('Salvar', new TAction([$this, 'save']), 'fa:save green'); 

        $this->form->addHeaderActionLink('Limpar', new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->form->addHeaderActionLink('Voltar', new TAction(['CategoryList', 'clear']), 'fa:arrow-left black');

        parent::add($this->form);
    }

    public function save($param)
    {
        echo '<pre>';
        var_dump($this->form->getData());
    }
}
