<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use DateTime;

class EphyCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('ephy')
            ->setDescription('...')
            ->addArgument('file', InputArgument::OPTIONAL, 'Argument description')
            ->addArgument('begin', InputArgument::OPTIONAL, 'Argument description')
            ->addArgument('end', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function log($s){
        $this->io->note(sprintf('%s : %s', (new DateTime())->format('Y-m-d H:i:s'), $s));
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $io = new SymfonyStyle($input, $output);
        $this->io = $io;
        $file = $input->getArgument('file');
        $begin = $input->getArgument('begin');
        $end = $input->getArgument('end');
        if ($input->getOption('option')) {
            // ...
        }

        $output->writeln('Command result.');
        $this->log($file." begin ".$begin." end ".$end);

        $xml = simplexml_load_file($file);
        $ppps = $xml->{'intrants'}->{'PPPs'};
        $total = 0;
        foreach ($ppps->children() as $ppp) {
            $total = $total+1;
        }
        $this->log("total ".$total);


        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $ephyrepository = $em->getRepository(EphyProduit::class);

        $ephyrepository->xml_file($file, $begin, $end);

        $this->log('Fin');

        return Command::SUCCESS;
    }

}
