<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Goutte\Client;
use Datetime;

use AppBundle\Entity\Annonce;

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

		$this->updateNew();
		$this->scrappe_leboncoins();
		$this->scrappe_leboncoin_appartements();
        $this->scrappe_agriaffaires();
	}

	protected function saveOrUpdate($array){
		$url = "https://www.maplaine.fr/annonces/api";
		//$url = "localhost:8000/annonces/api";

		foreach($array as $annonce){
			print($annonce->title."\n");
			print($annonce->category."\n");
		}
		//return;
        $postfield = array(
            "annonces" => json_encode($array),
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfield);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
		print $response;
	}

	protected function updateNew(){
		$url = "https://www.maplaine.fr/annonces/api/update_new";
		//$url = "localhost:8000/annonces/api/update_new";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
		print $response;
	}

	/**
	 *
	 * LEBONCOIN
	 *
	 **/


    protected function scrappe_leboncoins(){
		$paths = ["https://www.leboncoin.fr/materiel_agricole/offres/champagne_ardenne/", "https://www.leboncoin.fr/materiel_agricole/offres/picardie/"];
		foreach($paths as $path){
			$this->scrappe_leboncoin($path);
			for ($i = 2; $i <= 3; $i++) {
	            $this->scrappe_leboncoin($path."p-".$i."/");
	        }
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
		print("path $path \n");
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		$response = curl_exec($ch);


		$pos = strpos($response, 'window.FLUX_STATE = ');
		$pos = $pos + 20;
		$pos2 = strpos($response, "</script>", $pos);

		$response = substr($response, $pos, $pos2-$pos);
		$response = json_decode($response);

		//print($response);
		$d = $response->adSearch->data;
		if(isset($d->ads)){
			$data = $d->ads;
		} else {
			$data = [];
		}

		$annonces = [];
		foreach($data as $d){
			$annonce = new Annonce();
			$annonce->new = true;
            $annonce->type = "leboncoin";
			$annonce->title = $d->subject;
			$annonce->price = 0;
			if(isset($d->price) && count($d->price)){
	            $annonce->price = intval($d->price[0]);
			}
            $annonce->url = $d->url;;
			$annonce->image = "";
			if(isset($d->images->thumb_url)){
				$annonce->image = $d->images->thumb_url;
			}
            $annonce->lastView = new Datetime();
            $annonce->log = json_encode($d);
            $annonce->clientId = $d->url;
            $annonce->description = "";
			$annonce->category = "";
			$annonces[] = $annonce;//print(json_encode($annonce)."\n\n");
		}
		$this->saveOrUpdate($annonces);
    }


	protected function scrappe_leboncoin_appartement($ville){
		$path = "https://www.leboncoin.fr/recherche/?category=9&region=8&region_near=1&cities=".$ville;
		print("path $path \n");
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		$response = curl_exec($ch);


		$pos = strpos($response, 'window.FLUX_STATE = ');
		$pos = $pos + 20;
		$pos2 = strpos($response, "</script>", $pos);

		$response = substr($response, $pos, $pos2-$pos);
		$response = json_decode($response);

		//print($response);
		$d = $response->adSearch->data;
		if(isset($d->ads)){
			$data = $d->ads;
		} else {
			$data = [];
		}
		
		$annonces = [];
		foreach($data as $d){
			$annonce = new Annonce();
			$annonce->new = true;
            $annonce->type = "leboncoin ".$ville;
			$annonce->title = $d->subject;
			$annonce->price = 0;
			if(isset($d->price) && count($d->price)){
	            $annonce->price = intval($d->price[0]);
			}
            $annonce->url = $d->url;;
			$annonce->image = "";
			if(isset($d->images->thumb_url)){
				$annonce->image = $d->images->thumb_url;
			}
            $annonce->lastView = new Datetime();
            $annonce->log = json_encode($d);
            $annonce->clientId = $d->url;
            $annonce->description = "";
			$annonce->category = "immobilier";

			if(strpos($annonce->title, "Terrain") !== false){
				$annonce->category = "terrain";
			}
			$annonces[] = $annonce;//print(json_encode($annonce)."\n\n");
		}
		$this->saveOrUpdate($annonces);
	}


	protected function scrappe_leboncoin_appartements(){
		$r = ["Pignicourt_02190", "Brimont_51220", "Fresne-l%C3%A8s-Reims_51110", "Loivre_51220", "Aum%C3%A9nancourt_51110", "Orainville_02190", "Berm%C3%A9ricourt_51220", "Brimont_51220", "Bourgogne_51110"];
		foreach ($r as $i) {
			$this->scrappe_leboncoin_appartement($i);

		}
    }

	/**
	 *
	 * AGRIAFFAIRE
	 *
	 **/

    protected function scrappe_agriaffaires(){
		$this->scrappe_agriaffaire("https://www.agriaffaires.com/occasion/tracteur-agricole/1-france_champagne-ardennes.html");
		$this->scrappe_agriaffaire("https://www.agriaffaires.com/occasion/tracteur-agricole/2-france_champagne-ardennes.html");
		$this->scrappe_agriaffaire("https://www.agriaffaires.com/occasion/tracteur-agricole/3-france_champagne-ardennes.html");
		$this->scrappe_agriaffaire("https://www.agriaffaires.com/occasion/tracteur-agricole/1-france_picardie.html");
		$this->scrappe_agriaffaire("https://www.agriaffaires.com/occasion/tracteur-agricole/2-france_picardie.html");
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
		$this->annonces = [];
		$crawler->filter('div[class="liste-simple"]')->each(function ($node) {
            $title = $node->filter('h3')->text();
            $title = superTrim($title);
            //print("\n***********".$title);
            $url = "https://www.agriaffaires.com".$node->filter('a')->attr('href');
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
			$annonce->new = true;
            $annonce->type = "agriaffaire";
			$annonce->title = $title;
            $annonce->price = intval($price);
            $annonce->url = $url;
            $annonce->image = $image;
			$annonce->lastView = new \DateTime();;
			$annonce->log = "";
			$annonce->clientId = $url;
			$annonce->description = $description;

			$this->annonces[] = $annonce;
		});
		$this->saveOrUpdate($this->annonces);

    }






}
