<?php

use Adianti\Control\TPage;
use Adianti\Control\TAction;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Database\TTransaction;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TPassword;
use Adianti\Wrapper\BootstrapFormBuilder;

class UserForm extends TPage
{
    private $form;

    use Adianti\Base\AdiantiStandardFormTrait;

    public function __construct()
    {
        parent::__construct();

        $this->setDatabase('olx');
        $this->setActiveRecord('UserOlxApi');

        $this->form = new BootstrapFormBuilder('form_user');
        $this->form->setFormTitle('Usuários');

        $this->form->setClientValidation(true);

        $id  = new TEntry('id');
        $name = new TEntry('name');
        $email = new TEntry('email');
        $state = new TDBCombo('state', 'olx', 'StatesOlxApi', 'id', 'name', 'name');
        $pass = new TPassword('password');
        $pass->minlength = 4;
        $state->enableSearch();

        $id->setEditable(FALSE);

        $this->form->addFields([new TLabel('Cód.')], [$id]);
        $this->form->addFields([new TLabel('Nome')], [$name]);
        $this->form->addFields([new TLabel('E-mail')], [$email]);
        $this->form->addFields([new TLabel('Estado')], [$state]);

        if (!isset($_REQUEST['key']) && $_REQUEST['method'] != 'save') {
            $this->form->addFields([new TLabel('Senha')], [$pass]);
            $pass->addValidation('Senha', new TRequiredValidator);
        }

        $name->addValidation('Nome', new TRequiredValidator);
        $state->addValidation('State', new TRequiredValidator);
        $email->addValidation('E-mail', new TRequiredValidator);


        $this->form->addHeaderAction('Salvar', new TAction([$this, 'save']), 'fa:save green');

        $this->form->addHeaderActionLink('Limpar', new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->form->addHeaderActionLink('Voltar', new TAction(['UserList', 'clear']), 'fa:arrow-left black');

        parent::add($this->form);
    }

    public function save()
    {
        try {
            if (empty($this->database)) {
                throw new Exception(AdiantiCoreTranslator::translate('^1 was not defined. You must call ^2 in ^3', AdiantiCoreTranslator::translate('Database'), 'setDatabase()', AdiantiCoreTranslator::translate('Constructor')));
            }

            if (empty($this->activeRecord)) {
                throw new Exception(AdiantiCoreTranslator::translate('^1 was not defined. You must call ^2 in ^3', 'Active Record', 'setActiveRecord()', AdiantiCoreTranslator::translate('Constructor')));
            }

            // open a transaction with database
            TTransaction::open($this->database);

            // get the form data
            $object = $this->form->getData($this->activeRecord);

            // validate data
            $this->form->validate();

            // md5 pass && md5 token
            $object->password = md5($object->password);
            $object->token = md5($object->name . date('h:i:s') . random_int(1, 5));

            // return var_dump($object->token);

            // stores the object
            $object->store();

            // fill the form with the active record data
            $this->form->setData($object);

            // close the transaction
            TTransaction::close();

            // shows the success message
            if (isset($this->useMessages) and $this->useMessages === false) {
                AdiantiCoreApplication::loadPageURL($this->afterSaveAction->serialize());
            } else {
                new TMessage('info', AdiantiCoreTranslator::translate('Record saved'), $this->afterSaveAction);
            }

            return $object;
        } catch (Exception $e) // in case of exception
        {
            // get the form data
            $object = $this->form->getData();

            // fill the form with the active record data
            $this->form->setData($object);

            // shows the exception error message
            new TMessage('error', $e->getMessage());

            // undo all pending operations
            TTransaction::rollback();
        }
    }
}
