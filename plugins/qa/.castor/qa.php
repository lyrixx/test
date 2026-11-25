<?php

namespace qa;

use Castor\Attribute\AsExposedContext;
use Castor\Attribute\AsExposedListener;
use Castor\Attribute\AsExposedTask;
use Castor\Context;
use Castor\Event\BeforeExecuteTaskEvent;

use function Acme\Qa\diff;
use function Castor\context;
use function Castor\io;

#[AsExposedContext(name: 'ci')]
function ci_context(): Context
{
    return new Context(['ci' => true]);
}

#[AsExposedTask(description: 'Exposed: checks the coding style')]
function cs(): void
{
    io()->writeln('qa:cs runs in ' . context()->workingDirectory);
    io()->write(diff("foo\n", "bar\n"));
}

#[AsExposedListener(BeforeExecuteTaskEvent::class)]
function announce(BeforeExecuteTaskEvent $event): void
{
    io()->writeln('[acme/qa exposed listener] before ' . $event->task->getName());
}
