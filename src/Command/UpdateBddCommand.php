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
        $this->em = $doctrine->getEntityManager();





        if ($input->getOption('option')) {
            // ...
        }

        $its = $this->em->getRepository('App:Intervention')->findByType("recolte");
        foreach ($its as $it2) {
            print("\n".$it2->type);
            $this->em->getRepository('App:Intervention')->delete($it2);
        }
        

        $livraison = $this->em->getRepository('App:Livraison')->findAll();
        foreach ($livraison as $l) {
            $name = "recolte ".$l->date->format("Y-m-d")." ".$l->parcelle->id;
            $it = $this->em->getRepository('App:Intervention')->findOneByName($name);
            print("\n".$name);
            
            if($it==null){
                print("\n   ".$name);
                $it = new Intervention();
                $it->type = "recolte";
                $it->name = $name;
                $it->datetime = $l->date;
                $it->campagne = $l->campagne;
                $it->company = $l->campagne->company;
                $it->surface = 0;
                $this->em->persist($it);
                $this->em->flush();
                $it_par = new InterventionParcelle();
                $it_par->parcelle = $l->parcelle;
                $it_par->intervention = $it;
                $this->em->persist($it_par);
                $this->em->flush();
                
            }
            $it_rec = new InterventionRecolte();
            $it_rec->intervention = $it;

            $it_rec->name = $l->name;
            $it_rec->datetime = $l->date;

            $it_rec->vehicule = $l->vehicule;
            $it_rec->espece = $l->espece;
            $it_rec->poid_total = $l->poid_total;
            $it_rec->tare = $l->tare;
            $it_rec->humidite = $l->humidite;
            $it_rec->impurete = $l->impurete;
            $it_rec->ps = $l->ps;
            $it_rec->proteine = $l->proteine;
            $it_rec->calibrage = $l->calibrage;
            $it_rec->poid_norme = $l->poid_norme;

            $this->em->persist($it_rec);
            $this->em->flush();
        }

        $output->writeln('Command result.');
    }

}
