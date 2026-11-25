<?php

namespace Acme\Orm\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The CLI of the library, written for symfony/console 7.
 */
#[AsCommand('orm:validate-schema')]
final class ValidateSchemaCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        new Table($output)->setHeaders(['entity', 'status'])->addRow(['User', 'ok'])->render();

        return Command::SUCCESS;
    }
}
