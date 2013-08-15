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

        $teams = $this->getPredis()->lrange('teams', 0, -1);

        if ($this->getPredis()->zadd('users', 0, $name)) {
            $team = $dialog->select(
                $output,
                'Add to which team?',
                $teams,
                0
            );
            $this->getPredis()->hmset("user:$name", 'team', $team);
            $output->writeln("<info>$name successfully added to {$teams[$team]}</info>");
        } else {
            $output->writeln("<error>Failed to add user: $name</error>");
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