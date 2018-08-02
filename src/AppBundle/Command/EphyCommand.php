<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $argument = $input->getArgument('argument');

        if ($input->getOption('option')) {
            // ...
        }

        $output->writeln('Command result.');
        print($argument);


        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $ephyrepository = $em->getRepository('AppBundle:EphyProduit');

        $ephyrepository->xml_file($argument);

    }

}
