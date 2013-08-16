<?php

use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Knp\Console\ConsoleEvent;
use Knp\Console\ConsoleEvents;
use MarioKartLeague\Commands\AddUserCommand;
use MarioKartLeague\Commands\DeleteUserCommand;
use Silex\Provider\DoctrineServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Provider\ConsoleServiceProvider;

$app = new Silex\Application();

$app['debug'] = true;

/**
 * APPLICATION CONFIGURATION
 *
 * (is added to $app as normal, but provides a way of isolating the configuration)
 */
$replacements = parse_ini_file(__DIR__.'/config/parameters.ini');
$env = getenv('APP_ENV') ?: 'prod';
$app->register(new Igorw\Silex\ConfigServiceProvider(__DIR__."/config/config.yml", $replacements));
$envConfig = __DIR__."/config/config_$env.yml";
if (file_exists($envConfig)) {
    $app->register(new Igorw\Silex\ConfigServiceProvider($envConfig));
}

/**
 * Service providers
 */
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/Resources/views',
));

$app->register(new ConsoleServiceProvider(), array(
    'console.name'              => 'MarioKartLeague',
    'console.version'           => '1.0.0',
    'console.project_directory' => __DIR__.'/..'
));

$app->register(new DoctrineServiceProvider, array(
    "db.options" => array(
        "driver" => $app['mariokartleague']['database']['driver'],
        "dbname" => $app['mariokartleague']['database']['name'],
        "host"   => $app['mariokartleague']['database']['host'],
        "user"   => $app['mariokartleague']['database']['user'],
        "password" => $app['mariokartleague']['database']['password']
    ),
));

$app->register(new DoctrineOrmServiceProvider, array(
    "orm.proxies_dir" => __DIR__."/cache/proxies",
    "orm.em.options" => array(
        "mappings" => array(
            // Using actual filesystem paths
            array(
                "type" => "annotation",
                "namespace" => "MarioKartLeague\\Entity",
                "path" => __DIR__."/../src/MarioKartLeague/Entity/",
            )
        ),
    ),
));


/**
 * Console commands
 */
$app['dispatcher']->addListener(ConsoleEvents::INIT, function(ConsoleEvent $event) {
    $app = $event->getApplication();
    $app->add(new AddUserCommand());
    $app->add(new DeleteUserCommand());
});



return $app;