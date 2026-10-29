<?php

namespace App\controllers;

use Slim\Routing\RouteCollectorProxy;

$app->group('/api', function (RouteCollectorProxy $api) {
    $cursoRoutes = function (RouteCollectorProxy $endpoint) {
        $endpoint->get('[/{id}]', Cursos::class . ':read');
        $endpoint->post('', Cursos::class . ':create');
        $endpoint->put('/{id}', Cursos::class . ':update');
        $endpoint->delete('/{id}', Cursos::class . ':delete');
    };
    $api->group('/cursos', $cursoRoutes);
    $api->group('/curso', $cursoRoutes);
    $api->group('/cursos_matricula', $cursoRoutes);
});
