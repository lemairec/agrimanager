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

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

use Symfony\Component\HttpKernel\KernelInterface;

class ExportBddCommand extends Command
{
    protected $projectDir;

    public function __construct(KernelInterface $kernel)
    {
        parent::__construct();
        $this->projectDir = $kernel->getProjectDir();
    }

    protected function configure()
    {
        $this
            ->setName('export_bdd')
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

        #$output->writeln($this->projectDir);

        $path = $this->projectDir."/temp/dump";

        
        $output->writeln("begin");

        //remove old
        $output->writeln("remove");
        $command = "rm -rf $path";
        $process= new Process($command);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        $output->writeln("remove ok");


        //remove old
        $output->writeln("mkdir");
        $command = "mkdir -p $path";
        $process= new Process($command);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        $output->writeln("mkdir ok");



        //backup
        $output->writeln("sql");
        $backupSqlFile = "$path/backup.sql";
        $host = getenv('DATABASE_HOST');
        $port = getenv('DATABASE_PORT');
        $db = getenv('DATABASE_NAME');
        $user = getenv('DATABASE_USER');
        $password = getenv('DATABASE_PASSWORD');
        $command = "mysqldump -u $user --password=$password --host=$host --port=$port --opt $db --max_allowed_packet=512M > $backupSqlFile";
        $process= new Process($command);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        #$output->writeln($backupSqlFile);
        $output->writeln('sql ok');

        $output->writeln("create zip");
        $zip = new \ZipArchive();
        $zipName = "$path/dump_maplaine.zip";
        $zip->open($zipName,  \ZipArchive::CREATE);

        $output->writeln("documents");
        foreach ($this->em->getRepository('App:Document')->findAll() as $f) {
            $file = $f->getDocName();
            if($file){
                $src = $this->projectDir."/public/uploads/documents/".$file;
                #$output->writeln($src);
                $zip->addFile($src, $file);
            }
        }

        $output->writeln("factures");
        $zip->addEmptyDir("factures");
        $factures = $this->em->getRepository('App:Gestion\FactureFournisseur')->findAll();
        $len = count($factures);
        $i = 0;
        foreach ($this->em->getRepository('App:Gestion\FactureFournisseur')->findAll() as $f) {
            $file = $f->getFactureFileName();
            if($file){
                $src = $this->projectDir."/public/uploads/factures/".$file;
                #$output->writeln($src);
                $zip->addFile($src, "factures/".$file);
            }
            $i = $i+1;
            if($i%($len/10) == 0){
                $perc = round($i/($len/100));
                $output->writeln("factures $perc %");
            }
        }
        $output->writeln("sql");
        
        $zip->addFile($backupSqlFile, "database.sql");
        $output->writeln("close");
       $zip->close();
    }

}
