<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ChangeEmailCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('app:user:change-email')
            ->setDescription('Change the email of a user.')
            ->setDefinition(array(
                new InputArgument('userId', InputArgument::REQUIRED, 'The user id'),
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
            ))
            ->setHelp(<<<EOT
The <info>app:user:change-email</info> command changes the email of a user:

  <info>php app/console app:user:change-email 1034 test@migmail.pl</info>

EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userId = $input->getArgument('userId');
        $email = $input->getArgument('email');

        $result = $this->getContainer()->get('user_manager')->changeEmail($userId, $email);

        if ($result === true) {
            $output->writeln(sprintf('Changed email for user <comment>%s</comment>', $userId));
        } else {
            $output->writeln(sprintf('An error occured while changing email for user <comment>%s</comment>, error: <comment>%s</comment>', $userId, $result));
        }

    }

}
