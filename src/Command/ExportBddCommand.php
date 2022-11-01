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


    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $container = $this->getApplication()->getKernel()->getContainer();
        $doctrine = $container->get('doctrine');
        $this->em = $doctrine->getManager();

        //backup
        $output->writeln("#sql");
        $path = $this->projectDir."/temp/dump";
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
        $output->writeln('#sql ok');

        return Command::SUCCESS;
    }

}
