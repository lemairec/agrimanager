<?php

namespace App\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use DateTime;

class EphyCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ephy')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function log($s){
        $this->io->note(sprintf('%s : %s', (new DateTime())->format('Y-m-d H:i:s'), $s));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $this->io = $io;
        $argument = $input->getArgument('argument');

        if ($input->getOption('option')) {
            // ...
        }

        $output->writeln('Command result.');

        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $ephyrepository = $em->getRepository('App:EphyProduit');

        $this->log($argument);
        $ephyrepository->xml_file($argument);

    }

}
