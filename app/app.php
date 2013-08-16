<?php

$app = require_once __DIR__.'/bootstrap.php';

$app->get('/', function() use($app) {
    /** @var \Doctrine\ORM\EntityManager $em */
    $em = $app['orm.em'];

    return $app['twig']->render("home.html.twig", array(
        'teams' => $em->getRepository('MarioKartLeague\\Entity\\Team')->findAll()
    ));
});
$app->get('/info', function() use($app) {
    phpinfo();
});

return $app;