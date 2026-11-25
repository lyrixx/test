<?php

namespace Castor\Attribute;

/**
 * A listener a plugin exposes to the projects registering it with
 * register_plugin(). A plain AsListener stays internal to the plugin: it only
 * exists in the plugin's own repository.
 */
#[\Attribute(\Attribute::TARGET_FUNCTION | \Attribute::IS_REPEATABLE)]
class AsExposedListener extends AsListener
{
}
