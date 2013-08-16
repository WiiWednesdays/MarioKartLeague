<?php
// cli-config.php
$app = require_once __DIR__ . DIRECTORY_SEPARATOR . 'app/bootstrap.php';

/** @var \Doctrine\ORM\EntityManager $em */
$em = $app['orm.em'];
$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em)
));