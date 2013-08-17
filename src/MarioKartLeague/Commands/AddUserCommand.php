<?php

namespace MarioKartLeague\Commands;

use MarioKartLeague\Entity\User;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddUserCommand extends AssignUserCommand
{
    protected function configure()
    {
        $this
            ->setName('user:add')
            ->setDescription('Add a new user and assign to a team')
            ->addArgument('name', InputArgument::OPTIONAL, 'Name of the user')
            ->addArgument('team_id', InputArgument::OPTIONAL, 'Id of the team')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getEntityManager();

        $name = $input->getArgument('name') ?: $this->getHelperSet()->get('dialog')->ask($output, "User's name: ");
        $user = $em->getRepository('MarioKartLeague\\Entity\\User')->findOneBy(array(
            'name' => $name
        ));
        if ($user) {
            $output->writeln("<error>User already exists, please provide a different name</error>");
            return;
        }

        try {
            $user = new User();
            $user->setName($name);
            $team = $this->assignTeam($user, $input, $output);
            $em->persist($user);
            $em->flush();

            if ($user->getId()) {
                $output->writeln(sprintf("<info>%s successfully added to %s</info>", $name, $team->getName()));
            } else {
                $output->writeln("<error>Failed to add user: $name</error>");
            }

        } catch (\RuntimeException $e) {
            $output->writeln(sprintf("<error>%s</error>", $e->getMessage()));
        }
    }
}