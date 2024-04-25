<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Twig\Environment;

class TwigIsBrokenCommand extends Command
{
    public function __construct(
        private readonly Environment $twig,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:twig-is-broken')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $extensions = $this->twig->getExtensions();
        foreach ($extensions as $extension) {
            dump($extension::class);
        }

        return Command::SUCCESS;
    }
}
