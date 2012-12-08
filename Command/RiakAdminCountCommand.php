<?php
namespace Kbrw\RiakBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class RiakAdminCountCommand extends ContainerAwareCommand
{
    
    protected $slugifier;
    
    protected function configure()
    {
        $this
            ->setName('riakAdmin:countKeys')
            ->setDescription('Count keys in a given bucket')
            //->addArgument('bucket', InputArgument::REQUIRED, 'Bucket name to be deleted')
            //->addOption("list", "l", InputOption::VALUE_NONE, "List keys as well")
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $service = $this->getContainer()->get("kbrw.content.type.normalizer");
        echo $service->getContentType("json") . "\n";
    }
}