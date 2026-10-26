<?php

namespace CouponURLS\Original\Commands\BuiltIn;

use CouponURLS\Original\Deployment\Builder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

Class BuildManagerCommand extends Command
{
    protected function configure()
    {
        $this->setName('build');
        $this->setDescription('Creates a compressed production version.');
        $this->addOption('target', null, InputOption::VALUE_REQUIRED);
        $this->addOption('compress', null, InputOption::VALUE_NONE);
        $this->addOption('silent', null, InputOption::VALUE_NONE, 'only outputs the result artifact path.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        (object) $builder = new Builder($input, $output);

        $builder->build();

        return 0;
    }
}
