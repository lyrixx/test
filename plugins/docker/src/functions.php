<?php

namespace Acme\Docker;

/**
 * A plain function: a project gets it as soon as it requires acme/docker,
 * without registering it.
 */
function compose(string ...$args): string
{
    return Compose::command(...$args);
}
