<?php

namespace MarioKartLeague\Commands;

use Knp\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteTeamCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('team:delete')
            ->setDescription('Delete a team')
            ->addArgument('name', InputArgument::OPTIONAL, 'Name of the team')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');
        $name = $input->getArgument('name') ?: $dialog->ask($output, "Team name: ");

        $em = $this->getEntityManager();

        $team = $em->getRepository('MarioKartLeague\\Entity\\Team')->findOneBy(array(
            'name' => $name
        ));
        if ($team) {
            $em->remove($team);
            $em->flush();
            $output->writeln("<info>Removed team: $name</info>");
        } else {
            $output->writeln("<info>Invalid team: $name</info>");
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