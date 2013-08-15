<?php

namespace MarioKartLeague\Commands;

use Knp\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddUserCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('mario:add-user')
            ->setDescription('Interactively add a new user')
            ->addArgument('name', InputArgument::OPTIONAL, 'Name of the user')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');
        $name = $input->getArgument('name') ?: $dialog->ask($output, "User's name: ");

        if ($this->getPredis()->sadd('users', $name)) {
            $output->writeln("<info>Added user, $name</info>");
        } else {
            $output->writeln("<error>Failed to add user, $name</error>");
        }
    }

    /**
     * @return \Predis\Client
     */
    protected function getPredis()
    {
        $app = $this->getSilexApplication();

        return $app['predis'];
    }
}