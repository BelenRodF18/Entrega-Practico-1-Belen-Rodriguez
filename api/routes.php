<?php

$router->get('/estado', 'EstadoController@ver');

$router->post('/login', 'AuthController@login');
$router->get('/sesion', 'AuthController@sesion');
$router->post('/logout', 'AuthController@logout');

$router->get('/articulos', 'ArticuloController@listar');

$router->get('/admin/resumen', 'AdminController@resumen');
$router->get('/admin/opciones', 'AdminController@opciones');
$router->get('/admin/usuarios', 'AdminController@usuarios');
$router->get('/admin/articulos', 'AdminController@articulos');
$router->get('/admin/articulos/:id', 'AdminController@verArticulo');
$router->post('/admin/articulos', 'AdminController@crearArticulo');
$router->put('/admin/articulos/:id', 'AdminController@actualizarArticulo');
$router->delete('/admin/articulos/:id', 'AdminController@eliminarArticulo');

$router->get('/usuario/perfil', 'UsuarioController@perfil');
$router->get('/usuario/prestamos', 'UsuarioController@prestamos');
$router->post('/usuario/prestamos', 'UsuarioController@pedirPrestamo');
$router->post('/usuario/prestamos/:id/devolver', 'UsuarioController@devolverPrestamo');
