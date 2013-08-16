<?php

namespace MarioKartLeague\Commands;

use Knp\Command\Command;
use MarioKartLeague\Entity\User;
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

        $em = $this->getEntityManager();

        $user = $em->getRepository('MarioKartLeague\\Entity\\User')->findBy(array(
            'name' => $name
        ));
        if ($user) {
            $output->writeln("<error>User already exists, please provide a different name</error>");
            return;
        }

        /** @var \MarioKartLeague\Entity\Team[] $teams */
        $teams = $em->getRepository('MarioKartLeague\\Entity\\Team')->findAll();
        $teamIds = array();
        foreach ($teams as $team) {
            $teamIds[] = $team->getId();
        }

        $user = new User();
        $user->setName($name);

//        if ($team = $dialog->select($output, 'Add to which team?', $teams)) {
//            $user->setTeamId($team);
//        }

        $em->persist($user);
        $em->flush();

        if ($user->getId()) {
            $output->writeln("<info>$name successfully added</info>");
        } else {
            $output->writeln("<error>Failed to add user: $name</error>");
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