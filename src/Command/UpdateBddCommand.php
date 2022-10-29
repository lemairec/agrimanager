<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\Intervention;
use App\Entity\InterventionParcelle;
use App\Entity\InterventionRecolte;

class UpdateBddCommand extends Command
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
        $container = $this->getApplication()->getKernel()->getContainer();
        $doctrine = $container->get('doctrine');
        $this->em = $doctrine->getManager();



        if ($input->getOption('option')) {
            // ...
        }
        $output->writeln('begin');
        
        /*$facture = $this->em->getRepository(FactureFournisseur::class)->findAll();
        foreach ($facture as $f) {
            $f->paiementDate = $f->date;
            $f->paiementOrder = 0;
            $this->em->persist($f);
            $this->em->flush();
        }*/
        $facture = $this->em->getRepository('App:Silo\Temperature')->getAllForBalise("0e53210a-da1d-11ea-be14-0242ac110005");

        $output->writeln('Command result.');
    }

}
