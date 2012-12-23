<?php
namespace Kbrw\RiakBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class RiakDebugCommand extends ContainerAwareCommand
{
    
    /**
     * @var \Kbrw\RiakBundle\Model\Cluster\Cluster
     */
    protected $cluster;
    
    protected function configure()
    {
        $this
            ->setName('riak:debug')
            ->setDescription('Debug stuffs')
            //->addArgument('bucket', InputArgument::REQUIRED, 'Bucket name to be deleted')
            //->addOption("list", "l", InputOption::VALUE_NONE, "List keys as well")
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->cluster = $this->getContainer()->get("riak.cluster.backend");
        $bucket = $this->cluster->getBucket("users", true);
        $bucket->delete($bucket->keys());
        $bucket->put(array("remi1" => new \Acme\DemoBundle\Model\User("remi1", "remi.alvado" . rand(1, 10000) . "@gmail.com")));
        $bucket->put(array("remi2" => new \Acme\DemoBundle\Model\User("remi2", "remi.alvado" . rand(1, 10000) . "@gmail.com")));
        //print_r($bucket->fetch("remi2"));
        //$bucket->enableSearchIndexing();
        //$bucket->disableSearchIndexing();
        //$bucket->save();
        print_r($bucket->sasearch(new \Kbrw\RiakBundle\Model\Search\Query("id:rem*", "id")));
    }
}