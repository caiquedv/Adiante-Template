<?php
require_once '../request.php';

try
{
    // $body['order'] = 'id';
    // $body['direction'] = 'asc';
    $location = 'http://localhost/adianti/template/olx';
    $ping = request($location, 'GET', [], 'Basic 123');
    
    print_r($ping);
}
catch (Exception $e)
{
    print $e->getMessage();
}