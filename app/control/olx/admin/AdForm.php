<?php

use Adianti\Control\TPage;
use Adianti\Control\TAction;
use Adianti\Widget\Form\TFile;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Database\TTransaction;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TRadioGroup;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\Validator\TRequiredValidator;
use Adianti\Wrapper\BootstrapFormBuilder;

class AdForm extends TPage
{
    private $form;

    use Adianti\Base\AdiantiStandardFormTrait;
    use Adianti\Base\AdiantiFileSaveTrait;

    public function __construct()
    {
        parent::__construct();

        $this->setDatabase('olx');
        $this->setActiveRecord('AdsOlxApi');

        $this->form = new BootstrapFormBuilder('form_ads');
        $this->form->setFormTitle('Anúncios');

        $this->form->setClientValidation(true);

        $id  = new TEntry('id');
        $status = new TRadioGroup('status');
        $user = new TDBCombo('user_id', 'olx', 'UserOlxApi', 'id', 'name', 'name');
        $state = new TDBCombo('state', 'olx', 'StatesOlxApi', 'id', 'name', 'name');
        $title = new TEntry('title');
        $category = new TDBCombo('category', 'olx', 'CategoriesOlxApi', 'id', 'name', 'name');
        $price = new TEntry('price');
        $priceNeg = new TRadioGroup('price_negotiable');
        $description = new TEntry('description');
        $imageupload  = new TFile('img');

        $imageupload->setAllowedExtensions(['gif', 'png', 'jpg', 'jpeg']);
        $imageupload->enableImageGallery();

        $id->setEditable(false);

        $boolOpts = [1 => 'Ativo', 0 => 'Inativo'];

        $status->addItems($boolOpts);
        $priceNeg->addItems($boolOpts);

        $status->setLayout('horizontal');
        $priceNeg->setLayout('horizontal');

        $user->enableSearch();
        $state->enableSearch();
        $category->enableSearch();

        $price->setNumericMask(2, ',', '.', true);
        $price->placeholder = "R$";

        $this->form->addFields([new TLabel('Cód.')], [$id]);
        $this->form->addFields([new TLabel('Status')], [$status]);
        $this->form->addFields([new TLabel('Anunciante')], [$user]);
        $this->form->addFields([new TLabel('Estado')], [$state]);
        $this->form->addFields([new TLabel('Título')], [$title]);
        $this->form->addFields([new TLabel('Categoria')], [$category]);
        $this->form->addFields([new TLabel('Preço')], [$price]);
        $this->form->addFields([new TLabel('Negociável')], [$priceNeg]);
        $this->form->addFields([new TLabel('Descrição')], [$description]);
        $this->form->addFields([new TLabel('Image Uploader')], [$imageupload]);

        $status->addValidation('Status', new TRequiredValidator);
        $user->addValidation('Anunciante', new TRequiredValidator);
        $state->addValidation('Estado', new TRequiredValidator);
        $title->addValidation('Título', new TRequiredValidator);
        $category->addValidation('Categoria', new TRequiredValidator);
        $price->addValidation('Preço', new TRequiredValidator);
        $priceNeg->addValidation('Negociável', new TRequiredValidator);
        $description->addValidation('Descrição', new TRequiredValidator);

        $this->form->addHeaderAction('Salvar', new TAction([$this, 'Save']), 'fa:save green');
        $this->form->addHeaderActionLink('Limpar', new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->form->addHeaderActionLink('Voltar', new TAction(['AdList', 'clear']), 'fa:arrow-left black');

        parent::add($this->form);
    }

    public function Save($param)
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

            // stores the object
            $object->store();

            // fill the form with the active record data
            $this->form->setData($object);

            // close the transaction
            TTransaction::close();

            // caique was here
            // echo '<pre>';
            // print_r($_FILES);
            // return print_r($object);

            if (isset($object->img) && $object->img) {
                // $this->saveFile($object, $param, 'img', 'app/images/');
                rename("tmp/" . $object->img, "app/images/" . $object->img);
            }


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
