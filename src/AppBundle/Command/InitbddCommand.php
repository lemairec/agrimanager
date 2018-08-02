<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use AppBundle\Entity\User;
use AppBundle\Entity\Ilot;
use AppBundle\Repository\IlotRepository;
use AppBundle\Entity\Company;
use AppBundle\Entity\Campagne;
use AppBundle\Entity\Produit;

class InitbddCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('initbdd')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function addData()
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $company = new Company();
        $company->name = "warmo";
        $company->adresse = "12 route";
        $em->persist($company);
        $campagne = new Campagne();
        $campagne->name = "2017-2018";
        $campagne->company = $company;
        $em->persist($campagne);
        $ilot = $em->getRepository('AppBundle:Ilot')->add("cote merlan", 5.54);
        $em->getRepository('AppBundle:Parcelle')->add($campagne, $ilot->id, "orge-cote-merlan", "orge", 5.54);
        $ilot = $em->getRepository('AppBundle:Ilot')->add("les holles galant", 3);
        $em->getRepository('AppBundle:Parcelle')->add($campagne, $ilot->id, "orge-holles-galant", "orge", 3);
        $ilot = $em->getRepository('AppBundle:Ilot')->add("la noue balinet", 9.68);
        $em->getRepository('AppBundle:Parcelle')->add($campagne, $ilot->id, "orge-noue-balinet", "orge", $ilot->surface);
        $ilot = $em->getRepository('AppBundle:Ilot')->add("chemin des canons", 5.68);
        $em->getRepository('AppBundle:Parcelle')->add($campagne, $ilot->id, "colza-chemin-canons", "colza", $ilot->surface);
        $ilot = $em->getRepository('AppBundle:Ilot')->add("chemin du mesnil", 32.94);
        $em->getRepository('AppBundle:Parcelle')->add($campagne, $ilot->id, "orge-bettrave", "orge", 7.22);
        $em->getRepository('AppBundle:Parcelle')->add($campagne, $ilot->id, "orge-ble", "orge", 2.93);
        $em->getRepository('AppBundle:Parcelle')->add($campagne, $ilot->id, "colza", "colza", 14.42);
        $em->getRepository('AppBundle:Parcelle')->add($campagne, $ilot->id, "ble-colza", "ble", 7.22);
        $em->getRepository('AppBundle:Parcelle')->add($campagne, $ilot->id, "ble-colza-bande", "ble", 1.15);
        $ilot = $em->getRepository('AppBundle:Ilot')->add("batterie moucherie", 19.6);
        $em->getRepository('AppBundle:Parcelle')->add($campagne, $ilot->id, "ble-pdt", "ble", 4.5);
        $em->getRepository('AppBundle:Parcelle')->add($campagne, $ilot->id, "ble-bettrave", "ble", 15.1);
        $em->persist($ilot);
        $em->flush();
    }

    function addUser(){
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $user = new User();
        $user->username = 'admin';
        $user->email = 'email@domain.com';

        $encoder = $this->getContainer()->get('security.password_encoder');
        $encoded = $encoder->encodePassword($user, 'admin');
        $user->password = $encoded;
        //$user->setPassword('3NCRYPT3D-V3R51ON');
        $user->enabled = true;
        $em->persist($user);
        $em->flush();
    }

    function ephy_csv(){
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $ephyrepository = $em->getRepository('EphyBundle:EphyProduit');
        $fileName = '/Users/lemairec/fablab/symfony_agri/data/usages_des_produits_autorises_v2_utf8_04052017.csv';
        if (($handle = fopen($fileName, "r")) !== FALSE) {
            echo("toto");
            $i = 0;
            while (($rows = fgetcsv($handle, null, ";")) !== FALSE) {
                if ($i == 0) { $i = 1;continue; }
                $ephyrepository->addRows($rows);
            }
        }
    }

    public function scrapper_ephy($letter){

        $this->output->writeln("######## ".$letter);
        $client = new Client();
        $crawler = $client->request('GET', 'http://e-phy.agriculture.gouv.fr/spe/spe'.$letter.$letter.'.htm');
        #print($crawler->text());
        $crawler->filter('body > table > tbody > tr > td > a')->each(function ($node) {
            $link = $node->attr('href');
            $this->scrapper_ephy_link($link);
        });
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $argument = $input->getArgument('argument');

        if ($input->getOption('option')) {
            // ...
        }
        $this->output = $output;
        $output->writeln('Init Bdd');
        $output->writeln('add Data');
        $this->addData();
        $this->addUser();

        $this->ephy_csv();
        $this->caj_csv();

        //for($i = 'a'; $i < 'z'; $i++){
        //    $this->scrapper_ephy($i);
        //}
    }

}
