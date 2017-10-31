<?php

namespace AnnonceBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Goutte\Client;
use Datetime;

use AnnonceBundle\Entity\Annonce;

function superTrim($string)
{
	$string = trim($string);
	$string = str_replace('\t', ' ',  $string);
	$string = preg_replace('/[ ]+/', ' ',  $string);
	return $string;
}

class ScrappeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('scrappe')
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
        $this->scrappe_leboncoins();
        $this->scrappe_agriaffaires();
    }

    protected function scrappe_leboncoins(){
        for ($i = 1; $i <= 3; $i++) {
            $this->scrappe_leboncoin("https://www.leboncoin.fr/materiel_agricole/offres/champagne_ardenne/?ps=8&o=".$i);
            $this->scrappe_leboncoin("https://www.leboncoin.fr/materiel_agricole/offres/picardie/?ps=8&o=".$i);
        }
    }

    public function leboncoin_time($time){
        $res = str_replace("Aujourd'hui", date('d M'), $time);
        $res = str_replace("Hier", date('d M',strtotime("-1 days")), $res);
        $res = str_replace("août", 'Aug', $res);
		$res = str_replace("Urgent", '', $res);
		$res = superTrim($res);
        //print("\n".$res."\n");
        $datetime = DateTime::createFromFormat ("d M, H:i", $res);
        //print($datetime->format("d M Y H i"));
        return $datetime;
    }

    public function leboncoin_price($price){
        $res = str_replace("€", "", $price);
        $res = str_replace(" ", "", $res);
        return $res;
    }

    protected function scrappe_leboncoin($path){
        print("\n".$path."\n");
        $client = new Client();
        $crawler = $client->request('GET', $path);
        //print($crawler->text());
        $crawler->filter('body > section > main > section > section > section > section > ul > li > a')->each(function ($node) {
            $url = $node->attr('href');
            $title = $node->filter('section[class="item_infos"] > h2')->text();
            $title = superTrim($title);
            $price = $node->filter('section[class="item_infos"] > h3')->text();
            $price = superTrim($this->leboncoin_price($price));
            $image = '';
            if(strlen(superTrim($node->filter('div[class="item_image"] > span')->html())) > 0){
                $image = $node->filter('div[class="item_image"] > span > span')->attr('data-imgsrc');
            }
            $time = $node->filter('section[class="item_infos"] >  aside')->text();
            $time = superTrim($time);
			//print("*******".$time);
            $datetime = $this->leboncoin_time($time);


            $annonce = new Annonce();
            $annonce->type = "leboncoin";
            $annonce->title = $title;
            $annonce->price = intval($price);
            $annonce->url = $url;
            $annonce->image = $image;
            $annonce->lastView = $datetime;
            $annonce->log = "";
            $annonce->clientId = $url;
            $annonce->description = "";

            $this->saveOrUpdate($annonce);
            //print($node->html());
            //
            //$this->scrapper_ephy_link($link);
        });
    }

    protected function scrappe_agriaffaires(){
        $this->scrappe_agriaffaire("https://www.agriaffaires.com/occasion/outil-non-anime/1-france_champagne-ardennes.html");
        $this->scrappe_agriaffaire("https://www.agriaffaires.com/occasion/outil-non-anime/1-france_picardie.html");
        $this->scrappe_agriaffaire("https://www.agriaffaires.com/occasion/benne-cerealiere/1-france_champagne-ardennes.html");
        $this->scrappe_agriaffaire("https://www.agriaffaires.com/occasion/benne-cerealiere/1-france_picardie.html");
        $this->scrappe_agriaffaire("https://www.agriaffaires.com/occasion/semoir/1-france_champagne-ardennes.html");
        $this->scrappe_agriaffaire("https://www.agriaffaires.com/occasion/semoir/1-france_picardie.html");
    }

    protected function scrappe_agriaffaire($path){
        print("\n".$path."\n");
        $client = new Client();
        $crawler = $client->request('GET', $path);
        //print($crawler->text());
        $crawler->filter('div[class="liste-simple"]')->each(function ($node) {
            $title = $node->filter('h3')->text();
            $title = superTrim($title);
            //print("\n***********".$title);
            $url = "https://agriaffaires.com".$node->filter('a')->attr('href');
            //print("\n***********".$url);
            $image = "";
            if($node->filter('img')->count() > 0){
                $image = $node->filter('img')->attr('src');
            }
            //print("\n***********".$image);
            $price= 0;
            if($node->filter('span[class="js-price"]')->count() > 0){
                $price = $this->leboncoin_price($node->filter('span[class="js-price"]')->text());
            }
            //print("\n***********".$price);
            $description = $node->filter('div[class="padding10"]')->text();
            $description = trim(preg_replace('~[\r\n]+~', ' ', $description));
        	//print("\n***********".$description);

            $annonce = new Annonce();
            $annonce->title = $title;
            $annonce->type = "agriaffaire";
            $annonce->price = intval($price);
            $annonce->url = $url;
            $annonce->image = $image;
            $annonce->lastView = new \Datetime();
            $annonce->description = $description;
            $annonce->log = "";
            $annonce->clientId = $url;

            if($annonce->price > 10){
                $this->saveOrUpdate($annonce);
            }
            //print($node->html());
            //
            //$this->scrapper_ephy_link($link);
        });

    }

    function saveOrUpdate($annonce){
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $annoncerepository = $em->getRepository('AnnonceBundle:Annonce');
        $annonce2 = $annoncerepository->findOneByClientId($annonce->url);
        if($annonce2 == null){
            $annonce->firstView = $annonce->lastView;
            print("\n*******");
            print("\n*******");
            print("\nlink :   ".$annonce->url);
            print("\ntitle :  ".$annonce->title);
            print("\nprice :  ".$annonce->price);
            $em->persist($annonce);
            $em->flush();
            //print("\nimage :  ".$image);
            //print("\ntime :   ".$time);

            //print("\n*******\n");
            return true;
        } else {
            print("find annonce \n");
            $annonce2->lastView = $annonce->lastView;
            $em->persist($annonce2);
            $em->flush();
            return false;
        }


    }




}
