<?php

require __DIR__.'/vendor/autoload.php';

use App\Application;
use App\Foo;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class RepackedApplication extends Application {}

$container = new ContainerBuilder();

$phpLoader = new PhpFileLoader($container, new FileLocator());
$instanceof = [];
$configurator = new ContainerConfigurator($container, $phpLoader, $instanceof, __DIR__, __FILE__);

$services = $configurator->services();
$services
    ->defaults()
        ->autowire()
        ->autoconfigure()
        ->public()

    ->load('App\\', __DIR__.'/src/*')
;

// The line that breaks everything
$services->set(Application::class, RepackedApplication::class);

$container->compile(true);

$foo = $container->get(Foo::class);
echo $foo->getApplicationName()."\n";
