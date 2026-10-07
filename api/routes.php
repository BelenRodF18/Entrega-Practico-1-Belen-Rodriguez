<?php
$router->get('/estado', 'EstadoController@ver');

$router->post('/login', 'AuthController@login');
$router->get('/sesion', 'AuthController@sesion');
$router->post('/logout', 'AuthController@logout');

$router->get('/admin/resumen', 'AdminController@resumen');
$router->get('/admin/articulos', 'AdminController@articulos');
