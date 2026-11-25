<?php

use function Castor\register_plugin;

\defined('CASTOR_USE_CHDIR') || \define('CASTOR_USE_CHDIR', true);

// The same plugin twice, once per application of the monorepo. acme/qa
// registers acme/docker in its castor.php, for its own repository: it is not
// installed here, and that registration is ignored.
register_plugin('acme/qa', prefix: 'project-a', workingDirectory: 'project/a');
register_plugin('acme/qa', prefix: 'project-b', workingDirectory: 'project/b');
