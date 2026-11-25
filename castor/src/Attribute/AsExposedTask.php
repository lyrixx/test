<?php

namespace Castor\Attribute;

/**
 * A task a plugin exposes to the projects registering it with
 * register_plugin(). A plain AsTask stays internal to the plugin: it only
 * exists in the plugin's own repository.
 */
#[\Attribute(\Attribute::TARGET_FUNCTION)]
class AsExposedTask extends AsTask
{
}
