<?php

namespace website;

use Castor\Attribute\AsContext;
use Castor\Attribute\AsListener;
use Castor\Attribute\AsTask;
use Castor\Context;
use Castor\Event\BeforeExecuteTaskEvent;
use Michelf\Markdown;

use function Castor\io;

// Internal: plain AsTask, AsContext and AsListener, they only exist in the repository of
// acme/qa. The file is still loaded by the projects registering acme/qa, where
// michelf/php-markdown (a require-dev) is missing: fine, as long as nothing
// runs at load time.

// The default one of the repository of acme/qa only: an exposed default
// context would become the default one of the projects registering it
#[AsContext(name: 'website', default: true)]
function website_context(): Context
{
    return new Context(['website' => true]);
}

#[AsTask(description: 'Internal: builds the website of acme/qa, with a require-dev package')]
function build(): void
{
    io()->write(Markdown::defaultTransform('# acme/qa'));
}

#[AsListener(BeforeExecuteTaskEvent::class)]
function internal_listener(BeforeExecuteTaskEvent $event): void
{
    io()->writeln('[acme/qa internal listener] before ' . $event->task->getName());
}
