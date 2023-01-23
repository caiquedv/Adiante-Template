<?php

use Adianti\Control\TWindow;
use Adianti\Widget\Template\THtmlRenderer;

class ModalWindowView extends TWindow
{
    public function __construct()
    {
        parent::__construct();
        
        parent::setSize( 0.6, null );
        parent::removePadding();
        parent::removeTitleBar();
        parent::disableEscape(); // desabilita fechar com esc - fechar feito com jquery no html
        
        $html = new THtmlRenderer('app/resources/modal.html');
        
        $replaces = [];
        $replaces['title']  = 'Título';
        $replaces['body']   = 'Conteúdo';
        $replaces['footer'] = 'Rodapé';
        
        $html->enableSection('main', $replaces);
        
        parent::add($html);
    }
}