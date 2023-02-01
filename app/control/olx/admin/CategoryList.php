<?php

use Adianti\Control\TPage;
use Adianti\Control\TAction;
use Adianti\Database\TFilter;
use Adianti\Database\TCriteria;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TButton;
use Adianti\Database\TRepository;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Widget\Util\TDropDown;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Wrapper\BootstrapFormBuilder;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Wrapper\BootstrapDatagridWrapper;

class CategoryList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;

    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();

        $this->setDatabase('olx');
        $this->setActiveRecord('CategoriesOlxApi');
        $this->addFilterField('name', 'like', 'name');
        $this->addFilterField('id', 'like', 'id');
        $this->addFilterField('slug', 'like', 'slug');
        $this->setDefaultOrder('id', 'asc');

        $this->form = new BootstrapFormBuilder();
        // $this->form->setFormTitle('Categorias');

        $name = new TEntry('name');
        $id = new TEntry('id');
        $slug = new TEntry('slug');

        $this->form->addFields([new TLabel('Nome')], [$name]);
        $this->form->addFields([new TLabel('Cód.')], [$id]);
        $this->form->addFields([new TLabel('Slug')], [$slug]);

        // $this->form->setData(TSession::getValue(__CLASS__ . '_filter_data'));

        $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->width = '100%';

        $col_id     = new TDataGridColumn('id', 'Cód.', 'right', '10%');
        $col_name   = new TDataGridColumn('name', 'Nome', 'left', '60%');
        $col_slug = new TDataGridColumn('slug', 'Slug', 'center', '30%');

        $col_id->setAction(new TAction([$this, 'onReload']), ['order' => 'id']);
        $col_name->setAction(new TAction([$this, 'onReload']), ['order' => 'name']);

        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_name);
        $this->datagrid->addColumn($col_slug);

        $action1 = new TDataGridAction(['CategoryForm', 'onEdit'], ['key' => '{id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['key' => '{id}']);

        $this->datagrid->addAction($action1, 'Editar', 'fa:edit blue');
        $this->datagrid->addAction($action2, 'Excluir', 'fa:trash-alt red');

        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));

        $panel = TPanelGroup::pack('Categorias', $this->datagrid);
        $panel->addFooter($this->pageNavigation);

        $panel->addHeaderActionLink(_t('New'), new TAction(['CategoryForm', 'onEdit']), 'fa:plus green');
        $panel->addHeaderActionLink('Filtros', new TAction([$this, 'onFilter']), 'fa:filter black');
        $panel->addHeaderActionLink('Limpar', new TAction([$this, 'clear']), 'fa:eraser red');

        $dropdown = new TDropDown('Exportar', 'fa:list');
        $dropdown->setButtonClass('btn btn-sm btn-default waves-effect dropdown-toggle');
        $dropdown->addAction('HTML', new TAction([$this, 'onGenerate'], ['html']), 'fa:file-code blue');
        $dropdown->addAction('PDF', new TAction([$this, 'onGenerate'], ['pdf']), 'fa:file-pdf red');
        $dropdown->addAction('RTF', new TAction([$this, 'onGenerate'], ['rtf']), 'fa:file black');
        $dropdown->addAction('XLS', new TAction([$this, 'onGenerate'], ['xls']), 'fa:file-excel green');

        $panel->addHeaderWidget($dropdown);

        // $vbox = new TVBox;
        // $vbox->style = 'width: 100%';
        // $vbox->add($panel);

        parent::add($panel);
    }

    public function onGenerate($param)
    {
        print_r($this->form->getData());
        return;

        try {
            TTransaction::open('olx');

            $data = $this->form->getData();

            $repository = new TRepository('CategoriesOlxApi');

            $criteria = new TCriteria;

            if ($data->name) {
                $criteria->add(new TFilter('name', 'like', "%{$data->name}%"));
            }

            if ($data->slug) {
                $criteria->add(new TFilter('slug', '=', "%{$data->slug}%"));
            }

            if ($data->id) {
                $criteria->add(new TFilter('id', '=', $data->id));
            }

            $categories = $repository->load($criteria);

            // echo '<pre>';
            // var_dump($clientes);
            // echo '</pre>';

            if ($categories) {
                $widths = [90, 300, 130, 0, 0];

                switch ($param[0]) {
                    case 'html':
                        $table = new TTableWriterHTML($widths);
                        break;
                    case 'pdf':
                        $table = new TTableWriterPDF($widths);
                        break;
                    case 'rtf':
                        $table = new TTableWriterRTF($widths);
                        break;
                    case 'xls':
                        $table = new TTableWriterXLS($widths);
                        break;
                }
                // id, nome, categoria, email, nascimento

                if (!empty($table)) {
                    $table->addStyle('header', 'Helvetica', '16', 'B', '#ffffff', '#4B5D8E');
                    $table->addStyle('title',  'Helvetica', '10', 'B', '#ffffff', '#617FC3');
                    $table->addStyle('datap',  'Helvetica', '10', '',  '#000000', '#E3E3E3', 'LR');
                    $table->addStyle('datai',  'Helvetica', '10', '',  '#000000', '#ffffff', 'LR');
                    $table->addStyle('footer', 'Helvetica', '10', '',  '#2B2B2B', '#B4CAFF');
                }

                $table->setHeaderCallback(function ($table) {
                    $table->addRow();
                    $table->addCell('Categorias', 'center', 'header', 5);

                    $table->addRow();
                    $table->addCell('Código', 'center', 'title');
                    $table->addCell('Nome', 'left', 'title');
                    $table->addCell('Slug', 'center', 'title');
                    // $table->addCell('Email', 'left', 'title');
                    // $table->addCell('Nascimento', 'center', 'title');
                });

                $table->setFooterCallback(function ($table) {
                    $table->addRow();
                    $table->addCell(date('Y-m-d H:i:s'), 'center', 'footer', 5);
                });

                $colore = false;
                foreach ($categories as $category) {
                    $style = $colore ? 'datap' : 'datai';

                    $table->addRow();
                    $table->addCell($category->id, 'center', $style);
                    $table->addCell($category->name, 'left', $style);
                    $table->addCell($category->slug, 'center', $style);

                    $colore = !$colore;
                }

                $output = 'app/output/tabular.' . $param[0];

                if (!file_exists($output) or is_writable($output)) {
                    $table->save($output);
                    parent::openFile($output);

                    new TMessage('info', 'Relatório gerado com sucesso');
                } else {
                    throw new Exception('Permissão negada: ' . $output);
                }
            } else {
                new TMessage('error', 'Cliente não existe');
            }

            $this->form->setData($data);

            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
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
