<?php

namespace AgriBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use AgriBundle\Entity\Ilot;
use AgriBundle\Repository\IlotRepository;
use AgriBundle\Entity\Company;
use AgriBundle\Entity\Campagne;
use AgriBundle\Entity\Produit;

use Goutte\Client;

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
        $ilot = $em->getRepository('AgriBundle:Ilot')->add("cote merlan", 5.54);
        $em->getRepository('AgriBundle:Parcelle')->add($campagne, $ilot->id, "orge-cote-merlan", "orge", 5.54);
        $ilot = $em->getRepository('AgriBundle:Ilot')->add("les holles galant", 3);
        $em->getRepository('AgriBundle:Parcelle')->add($campagne, $ilot->id, "orge-holles-galant", "orge", 3);
        $ilot = $em->getRepository('AgriBundle:Ilot')->add("la noue balinet", 9.68);
        $em->getRepository('AgriBundle:Parcelle')->add($campagne, $ilot->id, "orge-noue-balinet", "orge", $ilot->surface);
        $ilot = $em->getRepository('AgriBundle:Ilot')->add("chemin des canons", 5.68);
        $em->getRepository('AgriBundle:Parcelle')->add($campagne, $ilot->id, "colza-chemin-canons", "colza", $ilot->surface);
        $ilot = $em->getRepository('AgriBundle:Ilot')->add("chemin du mesnil", 32.94);
        $em->getRepository('AgriBundle:Parcelle')->add($campagne, $ilot->id, "orge-bettrave", "orge", 7.22);
        $em->getRepository('AgriBundle:Parcelle')->add($campagne, $ilot->id, "orge-ble", "orge", 2.93);
        $em->getRepository('AgriBundle:Parcelle')->add($campagne, $ilot->id, "colza", "colza", 14.42);
        $em->getRepository('AgriBundle:Parcelle')->add($campagne, $ilot->id, "ble-colza", "ble", 7.22);
        $em->getRepository('AgriBundle:Parcelle')->add($campagne, $ilot->id, "ble-colza-bande", "ble", 1.15);
        $ilot = $em->getRepository('AgriBundle:Ilot')->add("batterie moucherie", 19.6);
        $em->getRepository('AgriBundle:Parcelle')->add($campagne, $ilot->id, "ble-pdt", "ble", 4.5);
        $em->getRepository('AgriBundle:Parcelle')->add($campagne, $ilot->id, "ble-bettrave", "ble", 15.1);
        $em->persist($ilot);
        $em->flush();

    }

    function scrapper_ephy_link($link){
        if(!$link){
            return;
        }
        try {
            $client = new Client();
            $crawler = $client->request('GET', 'http://e-phy.agriculture.gouv.fr/spe/'.$link);
            $crawler->filter('div')->each(function ($node) {
                $text = $node->text();
                if (0 === strpos($text, 'Intrant: ')) {
                    $this->intrant = substr($text, 9, strlen($text));
                }
                if (0 === strpos($text, 'Numéro d\'autorisation: ')) {
                    $this->amm = substr($text, 24, strlen($text));
                }
            });
            $em = $this->getContainer()->get('doctrine')->getEntityManager();
            $produit = new Produit();
            $produit->amm = $this->amm;
            $produit->name = $this->intrant;
            $produit->no_ephy = explode('.',$link)[0];
            $produit = $em->getRepository('AgriBundle:Produit')->save($produit);
            $this->output->writeln(json_encode($produit));
        } catch (Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
            echo 'Exception reçue : ',  $e->getMessage(), "\n";
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
        for($i = 'a'; $i < 'z'; $i++){
            $this->scrapper_ephy($i);
        }
    }

}
