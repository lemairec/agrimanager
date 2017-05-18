<?php

namespace AgriBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
        $argument = $input->getArgument('argument');

        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $achats = $em->getRepository('AgriBundle:Achat')->findAll();
        foreach ($achats as $a) {
            $produit = $a->produit;
            $produit->unity = $a->unity;
            $produit->completeName = $produit->name." - ".$produit->unity;
            $em->persist($produit);
            $em->flush();
        }

        if ($input->getOption('option')) {
            // ...
        }

        $output->writeln('Command result.');
    }

}
