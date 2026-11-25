<?php

namespace Acme\Qa;

use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

/**
 * A plain function: a project gets it as soon as it requires acme/qa, without
 * registering it.
 */
function diff(string $from, string $to): string
{
    return new Differ(new UnifiedDiffOutputBuilder("--- before\n+++ after\n"))->diff($from, $to);
}
