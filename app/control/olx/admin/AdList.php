<?php

use Adianti\Control\TPage;
use Adianti\Control\TAction;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Util\TImage;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TRadioGroup;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Wrapper\BootstrapFormBuilder;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Wrapper\BootstrapDatagridWrapper;

class AdList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;

    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();

        $this->setDatabase('olx');
        $this->setActiveRecord('AdsOlxApi');

        $this->addFilterField('id', '=', 'id');
        $this->addFilterField('status', '=', 'status');
        $this->addFilterField('user_id', '=', 'user_id');
        $this->addFilterField('state', '=', 'state');
        $this->addFilterField('title', 'like', 'title');
        $this->addFilterField('category', '=', 'category');
        $this->addFilterField('price_negotiable', '=', 'price_negotiable');
        $this->addFilterField('description', 'like', 'description');
        $this->addFilterField('price', '>=', 'price_up');
        $this->addFilterField('price', '<=', 'price_down');

        $this->form = new BootstrapFormBuilder();
        $this->form->setFormTitle('Buscar Anúncio');

        $id  = new TEntry('id');
        $status = new TRadioGroup('status');
        $user = new TDBCombo('user_id', 'olx', 'UserOlxApi', 'id', 'name', 'name');
        $state = new TDBCombo('state', 'olx', 'StatesOlxApi', 'id', 'name', 'name');
        $title = new TEntry('title');
        $category = new TDBCombo('category', 'olx', 'CategoriesOlxApi', 'id', 'name', 'name');
        $priceUp = new TEntry('price_up');
        $priceDown = new TEntry('price_down');
        $priceNeg = new TRadioGroup('price_negotiable');
        $description = new TEntry('description');

        $boolOpts = [1 => 'Ativo', 0 => 'Inativo'];
        $status->addItems($boolOpts);
        $priceNeg->addItems($boolOpts);
        $status->setLayout('horizontal');
        $priceNeg->setLayout('horizontal');

        $user->enableSearch();
        $state->enableSearch();
        $category->enableSearch();

        $this->form->addFields([new TLabel('Cód.')], [$id]);
        $this->form->addFields([new TLabel('Status')], [$status]);
        $this->form->addFields([new TLabel('Anunciante')], [$user]);
        $this->form->addFields([new TLabel('Estado')], [$state]);
        $this->form->addFields([new TLabel('Título')], [$title]);
        $this->form->addFields([new TLabel('Categoria')], [$category]);
        $this->form->addFields([new TLabel('Preço Acima de')], [$priceUp]);
        $this->form->addFields([new TLabel('Preço Abaixo de')], [$priceDown]);
        $this->form->addFields([new TLabel('Negociável')], [$priceNeg]);
        $this->form->addFields([new TLabel('Descrição')], [$description]);

        $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->width = '100%';

        $col_id     = new TDataGridColumn('id', 'Cód.', 'right', '5%');
        $col_status   = new TDataGridColumn('status', 'Status', 'left', '10%');
        $col_user = new TDataGridColumn('user->name', 'Anunciante', 'center', '10%');
        $col_state = new TDataGridColumn('stateObj->name', 'Estado', 'center', '10%');
        $col_title = new TDataGridColumn('title', 'Título', 'center', '10%');
        $col_category = new TDataGridColumn('categoryObj->name', 'Categoria', 'center', '10%');
        $col_price = new TDataGridColumn('price', 'Preço', 'center', '10%');
        $col_negotiable = new TDataGridColumn('price_negotiable', 'Negociável', 'center', '10%');
        $col_description = new TDataGridColumn('description', 'Descrição', 'center', '80%');
        $col_image = new TDataGridColumn('img', 'Image', 'center', '20%');

        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_status);
        $this->datagrid->addColumn($col_user);
        $this->datagrid->addColumn($col_state);
        $this->datagrid->addColumn($col_title);
        $this->datagrid->addColumn($col_category);
        $this->datagrid->addColumn($col_price);
        $this->datagrid->addColumn($col_negotiable);
        $this->datagrid->addColumn($col_description);
        $this->datagrid->addColumn($col_image);

        $action1 = new TDataGridAction(['AdForm', 'onEdit'], ['key' => '{id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['key' => '{id}']);

        $this->datagrid->addAction($action1, 'Editar', 'fa:edit blue');
        $this->datagrid->addAction($action2, 'Excluir', 'fa:trash-alt red');

        $enableActive = function ($value) {
            return ($value) ? 'Ativo' : 'Inativo';
        };

        $format_price = function ($valor, $objeto, $row) {
            if (is_numeric($valor)) {
                return 'R$ ' . number_format($valor, 2, ',', '.');
            }
            return $valor;
        };

        $format_image = function($image) {
            $image = new TImage($image);
            $image->style = 'max-width: 140px; max-height: 100px';
            return $image;
        };

        $col_status->setTransformer($enableActive);
        $col_negotiable->setTransformer($enableActive);
        $col_price->setTransformer($format_price);
        $col_image->setTransformer($format_image);

        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));

        $panel = TPanelGroup::pack('Anúncios', $this->datagrid);
        $panel->addFooter($this->pageNavigation);

        $panel->addHeaderActionLink(_t('New'), new TAction(['AdForm', 'onEdit']), 'fa:plus green');
        $panel->addHeaderActionLink('Filtros', new TAction([$this, 'onFilter']), 'fa:filter black');
        $panel->addHeaderActionLink('Limpar', new TAction([$this, 'clear']), 'fa:eraser red');

        parent::add($panel);
    }

    public static function onFilter()
    {
        try {
            $page = new TPage;
            $page->setTargetContainer('adianti_right_panel');
            $page->setProperty('override', 'true');
            $page->setPageName(__CLASS__);

            $btn_close = new TButton('closeCurtain');
            $btn_close->onClick = "Template.closeRightPanel();";
            $btn_close->setLabel("Fechar");
            $btn_close->setImage('fas:times');

            $embed = new self();
            $embed->form->addHeaderWidget($btn_close);

            $page->add($embed->form);
            $page->setIsWrapped(true);
            $page->show();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    public function clear()
    {
        $this->clearFilters();
        $this->onReload();
    }
}
