<?php

namespace MarioKartLeague\Commands;

use Knp\Command\Command;
use MarioKartLeague\Entity\User;
use MarioKartLeague\Entity\Team;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AssignUserCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('user:assign')
            ->setDescription('Assign an existing user to a team')
            ->addArgument('name', InputArgument::OPTIONAL, 'Name/id of the user')
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

        $id = $input->getArgument('name') ?: $this->getHelperSet()->get('dialog')->ask($output, "Name/id of the user: ");
        $user = (is_numeric($id))
            ? $em->getRepository('MarioKartLeague\\Entity\\User')->find($id)
            : $em->getRepository('MarioKartLeague\\Entity\\User')->findOneBy(array('name' => $id))
        ;
        if (!$user) {
            $output->writeln("<error>User not found</error>");
            return;
        }

        $team = $this->assignTeam($user, $input, $output);
        $em->persist($user);
        $em->flush();

        $output->writeln(sprintf("<info>%s successfully assigned to %s</info>", $user->getName(), $team->getName()));
    }

    /**
     * @param User $user
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return Team
     * @throws \RuntimeException
     */
    protected function assignTeam(User $user, InputInterface $input, OutputInterface $output)
    {
        $em = $this->getEntityManager();

        /** @var \MarioKartLeague\Entity\Team[] $teams */
        $teams = $em->getRepository('MarioKartLeague\\Entity\\Team')->findAll();
        if (empty($teams)) {
            throw new \RuntimeException('There must be at least one team');
        }

        $teamIds = array();
        foreach ($teams as $team) {
            $teamIds[$team->getId()] = $team->getName();
        }

        $team_id = $input->getArgument('team_id') ?: $this->getHelperSet()->get('dialog')->select($output, 'Assign to which team?', $teamIds);
        $team = $em->getRepository('MarioKartLeague\\Entity\\Team')->find($team_id);
        $user->setTeam($team);

        return $team;
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