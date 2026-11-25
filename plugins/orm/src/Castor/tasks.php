<?php

namespace orm;

use Acme\Orm\Command\ValidateSchemaCommand;
use Castor\Attribute\AsExposedTask;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\ArrayInput;

use function Castor\io;
use function Castor\output;

#[AsExposedTask(description: 'Exposed: validates the schema with the CLI of the library')]
function validate_schema(): int
{
    // Which copy of symfony/console PHP loaded: the one of Castor, or the one of the vendor/ directory of the plugin
    io()->writeln('Command loaded from ' . new \ReflectionClass(Command::class)->getFileName());
    io()->writeln('Table loaded from ' . new \ReflectionClass(Table::class)->getFileName());

    return new ValidateSchemaCommand()->run(new ArrayInput([]), output());
}
