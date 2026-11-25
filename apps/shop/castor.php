<?php

use Castor\Attribute\AsTask;

use function Acme\Docker\compose;
use function Acme\Qa\diff;
use function Castor\io;
use function Castor\register_plugin;

\defined('CASTOR_USE_CHDIR') || \define('CASTOR_USE_CHDIR', true);

// Its exposed tasks, contexts and listeners only, from the files it autoloads
register_plugin('acme/docker');

// acme/qa is required but not registered: its functions and classes only

#[AsTask(description: 'Uses the functions of acme/docker and acme/qa')]
function deploy(): void
{
    io()->writeln(compose('pull'));
    io()->write(diff("v1\n", "v2\n"));
}
