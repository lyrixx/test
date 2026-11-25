<?php

namespace docker;

use Castor\Attribute\AsExposedContext;
use Castor\Attribute\AsExposedTask;
use Castor\Attribute\AsTask;
use Castor\Context;

use function Acme\Docker\compose;
use function Castor\context;
use function Castor\io;

#[AsExposedContext(name: 'docker')]
function docker_context(): Context
{
    return new Context(['docker' => true]);
}

#[AsExposedTask(description: 'Exposed: starts the stack')]
function up(): void
{
    io()->writeln(compose('up', '-d') . ' in ' . context()->workingDirectory);
}

#[AsTask(description: 'Internal: a plain AsTask, never exposed')]
function internal(): void
{
}
