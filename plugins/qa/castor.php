<?php

use function Castor\import;
use function Castor\register_plugin;

\defined('CASTOR_USE_CHDIR') || \define('CASTOR_USE_CHDIR', true);

import(__DIR__ . '/.castor');

// For the repository of acme/qa only: acme/docker is in its require-dev. A
// project registering acme/qa does not get it, and does not need it installed.
register_plugin('acme/docker');
