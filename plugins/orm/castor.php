<?php

use Castor\Attribute\AsTask;
use Symfony\Component\Yaml\Yaml;

use function Castor\import;
use function Castor\io;

\defined('CASTOR_USE_CHDIR') || \define('CASTOR_USE_CHDIR', true);

import(__DIR__ . '/src/Castor');

#[AsTask(namespace: 'release', description: 'Internal: reads the release config with symfony/yaml 7 (require-dev)')]
function prepare(): void
{
    // Which copy of symfony/yaml PHP loaded: the one of Castor, or the one of the vendor/ directory of the plugin
    io()->writeln('Yaml loaded from ' . new \ReflectionClass(Yaml::class)->getFileName());
    io()->writeln(Yaml::dump(['version' => '1.0.0']));
}
