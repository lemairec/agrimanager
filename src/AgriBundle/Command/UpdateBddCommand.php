<?php

namespace AgriBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AgriBundle\Entity\User;

class UpdateBddCommand extends ContainerAwareCommand
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

    protected function updateEphy(){
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $em->getRepository('AgriBundle:EphyProduit')->xml();
    }

    function addUser($username, $email, $password){
        $this->output->writeln('addUser '.$username);
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $user = $em->getRepository('AgriBundle:User')->findOneByUsername($username);
        if($user == null){
            $user = new User();
            $user->username = $username;
        }
        $user->email = $email;

        $encoder = $this->getContainer()->get('security.password_encoder');
        $encoded = $encoder->encodePassword($user, $password);
        $user->password = $encoded;
        //$user->setPassword('3NCRYPT3D-V3R51ON');
        $user->enabled = true;
        $em->persist($user);
        $em->flush();
        $company = $em->getRepository('AgriBundle:Company')->findOrCreate($user);
        $em->getRepository('AgriBundle:Campagne')->findFirstOrCreate($company, '2017-2018');
    }

    function updateCajCsv(){
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $achatrepository = $em->getRepository('AgriBundle:Achat');
        $fileName = 'data/caj.csv';
        $achatrepository->addCajCsv($fileName);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $argument = $input->getArgument('argument');

        /*$em = $this->getContainer()->get('doctrine')->getEntityManager();
        $achats = $em->getRepository('AgriBundle:Achat')->findAll();
        foreach ($achats as $a) {
            $produit = $a->produit;
            $produit->unity = $a->unity;
            $produit->completeName = $produit->name." - ".$produit->unity;
            $em->persist($produit);
            $em->flush();
        }*/

        if ($input->getOption('option')) {
            // ...
        }

        //$this->addUser('lejard', 'lemairec02@gmail.com', '');
        //$this->addUser('steph', 'steph@toto.fr', '');
        //$this->addUser('ceta', 'ceta@toto.fr', '');
        //$this->updateEphy();
        //$this->updateCajCsv();

        $output->writeln('Command result.');
    }

}
