<?php

namespace MarioKartLeague\Commands;

use Knp\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteUserCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('mario:delete-user')
            ->setDescription('Interactively delete a user')
            ->addArgument('name', InputArgument::OPTIONAL, 'Name of the user')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');
        $name = $input->getArgument('name') ?: $dialog->ask($output, "User's name: ");

        $em = $this->getEntityManager();

        $user = $em->getRepository('MarioKartLeague\\Entity\\User')->findOneBy(array(
            'name' => $name
        ));
        if ($user) {
            $em->remove($user);
            $em->flush();
            $output->writeln("<info>Removed user: $name</info>");
        } else {
            $output->writeln("<info>Invalid user: $name</info>");
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