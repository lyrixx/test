<?php

namespace Castor\Attribute;

/**
 * A context a plugin exposes to the projects registering it with
 * register_plugin(). A plain AsContext stays internal to the plugin: it only
 * exists in the plugin's own repository.
 */
#[\Attribute(\Attribute::TARGET_FUNCTION)]
class AsExposedContext extends AsContext
{
}
