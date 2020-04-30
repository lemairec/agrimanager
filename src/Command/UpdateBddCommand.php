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

        $facture = $this->em->getRepository('App:FactureFournisseur')->findAll();
        $company = $this->em->getRepository('App:Company')->find("f49d127e-3951-11e7-92c4-80e65014bb7c");
        foreach ($facture as $f) {
            if($f->company){
                print("ignore");
                continue;
            }
            $f->company = $company;

            $this->em->persist($f);
            $this->em->flush();
        }

        $facture = $this->em->getRepository('App:Operation')->findAll();
        $company = $this->em->getRepository('App:Company')->find("f49d127e-3951-11e7-92c4-80e65014bb7c");
        foreach ($facture as $f) {
            if($f->company){
                print("ignore");
                continue;
            }
            $f->company = $company;

            $this->em->persist($f);
            $this->em->flush();
        }

        $output->writeln('Command result.');
    }

}
