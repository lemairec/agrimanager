<?php

namespace AgriBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AgriBundle\Entity\User;

class UpdateBddCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('update_bdd')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $argument = $input->getArgument('argument');
        $em = $this->getContainer()->get('doctrine')->getEntityManager();




        if ($input->getOption('option')) {
            // ...
        }

        $produits = $em->getRepository('AgriBundle:Produit')->findAll();
        foreach ($produits as $p) {
            print($p->name);
             $em->getRepository('AgriBundle:Produit')->update($p);
        }

        $output->writeln('Command result.');
    }

}
