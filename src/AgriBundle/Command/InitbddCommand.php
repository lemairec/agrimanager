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
        $compagny_id = $company->id;
        $ilot = $em->getRepository('AgriBundle:Ilot')->add("cote merlan", 5.54);
        $em->getRepository('AgriBundle:Parcelle')->add($ilot->id, 2017, "orge-cote-merlan", "orge", 5.54);
        $ilot = $em->getRepository('AgriBundle:Ilot')->add("les holles galant", 3);
        $em->getRepository('AgriBundle:Parcelle')->add($ilot->id, 2017, "orge-holles-galant", "orge", 3);
        $ilot = $em->getRepository('AgriBundle:Ilot')->add("la noue balinet", 9.68);
        $em->getRepository('AgriBundle:Parcelle')->add($ilot->id, 2017, "orge-noue-balinet", "orge", $ilot->surface);
        $ilot = $em->getRepository('AgriBundle:Ilot')->add("chemin des canons", 5.68);
        $parcelle1 = $em->getRepository('AgriBundle:Parcelle')->add($ilot->id, 2017, "colza-chemin-canons", "colza", $ilot->surface);
        $ilot = $em->getRepository('AgriBundle:Ilot')->add("chemin du mesnil", 32.94);
        $em->getRepository('AgriBundle:Parcelle')->add($ilot->id, 2017, "orge-bettrave", "orge", 7.22);
        $em->getRepository('AgriBundle:Parcelle')->add($ilot->id, 2017, "orge-ble", "orge", 2.93);
        $parcelle2 = $em->getRepository('AgriBundle:Parcelle')->add($ilot->id, 2017, "colza", "colza", 14.42);
        $em->getRepository('AgriBundle:Parcelle')->add($ilot->id, 2017, "ble-colza", "ble", 7.22);
        $em->getRepository('AgriBundle:Parcelle')->add($ilot->id, 2017, "ble-colza-bande", "ble", 1.15);
        $ilot = $em->getRepository('AgriBundle:Ilot')->add("batterie moucherie", 19.6);
        $em->getRepository('AgriBundle:Parcelle')->add($ilot->id, 2017, "ble-pdt", "ble", 4.5);
        $em->getRepository('AgriBundle:Parcelle')->add($ilot->id, 2017, "ble-bettrave", "ble", 15.1);
        $parcelles =[$parcelle1, $parcelle2];
        $em->getRepository('AgriBundle:Intervention')->add("semis", "2016-08-01", $parcelles);
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
            $this->output->writeln(json_encode($produit));
            $em->getRepository('AgriBundle:Produit')->save($produit);
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
