<?php

namespace MeteoBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Goutte\Client;

use DateTime;

class MeteoCronCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('meteo_cron')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function update(){
        $client = new Client();
        $crawler = $client->request('GET', 'http://api.openweathermap.org/data/2.5/forecast?q=Warmeriville,fr&lang=fr&units=metric&appid=08733bae355af51820ad53268746027d');
        //$jsonData = $crawler->text();
        //print($jsonData);
        $data_json = $client->getResponse()->getContent();
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $em->getRepository('MeteoBundle:MeteoPrevision')->save_data_json($data_json, "Warmeriville", "ow");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $argument = $input->getArgument('argument');

        if ($input->getOption('option')) {
            // ...
        }

        while(true){
            $this->update();
            sleep(1*60);
        }

        $output->writeln('Command result.');
    }

}
