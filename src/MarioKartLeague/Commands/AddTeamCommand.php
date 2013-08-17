<?php

namespace MarioKartLeague\Commands;

use Knp\Command\Command;
use MarioKartLeague\Entity\Team;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddTeamCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('team:add')
            ->setDescription('Add a new team')
            ->addArgument('team', InputArgument::OPTIONAL, 'Name of the team')
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

        $dialog = $this->getHelperSet()->get('dialog');
        $name = $input->getArgument('team') ?: $dialog->ask($output, "Team name: ");
        $team = $em->getRepository('MarioKartLeague\\Entity\\Team')->findBy(array(
            'name' => $name
        ));
        if ($team) {
            $output->writeln("<error>Team already exists, please provide a different name</error>");
            return;
        }

        $team = new Team();
        $team->setName($name);
        $em->persist($team);
        $em->flush();

        if ($team->getId()) {
            $output->writeln("<info>$name successfully added</info>");
        } else {
            $output->writeln("<error>Failed to add team: $name</error>");
        }
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        $app = $this->getSilexApplication();
        return $app['orm.em'];
    }
}