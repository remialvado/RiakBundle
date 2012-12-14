<?php
namespace Kbrw\RiakBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class ListKeysCommand extends ContainerAwareCommand
{
    
    const OPTION_BUCKET  = "bucket";
    const OPTION_CLUSTER = "cluster";
    const OPTION_RAW     = "raw";
    
    protected function configure()
    {
        $this
            ->setName('riak:bucket:list')
            ->setDescription('List all keys inside a bucket.')
            ->addOption(self::OPTION_BUCKET,  "b", InputOption::VALUE_REQUIRED, "Bucket name")
            ->addOption(self::OPTION_CLUSTER, "c", InputOption::VALUE_REQUIRED, "Cluster name")
            ->addOption(self::OPTION_RAW,     "r", InputOption::VALUE_NONE,     "Display keys without style.")
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');
        
        $bucketName = $input->getOption(self::OPTION_BUCKET);
        while (empty($bucketName)) {
            $bucketName = $dialog->ask($output, 'Bucket name : ', null);
        }
        $clusterName = $input->getOption(self::OPTION_CLUSTER);
        while (empty($clusterName)) {
            $clusterName = $dialog->ask($output, 'Cluster name : ', null);
        }
        if (!$this->getContainer()->has("riak.cluster.$clusterName")) {
            $output->writeln("<error>Cluster '$clusterName' does not exist.</error>");
            return 1;
        }
        $keys = $this->getContainer()->get("riak.cluster.$clusterName")->getBucket($bucketName)->keys();
        if ($input->hasOption(self::OPTION_RAW) && $input->getOption(self::OPTION_RAW)) {
            echo implode(" ", $keys);
        }
        else {
            $output->writeln("<info>Key(s) for '$bucketName' bucket : </info>");
            echo " - " . implode("\n - ", $keys) . "\n";
        }
        return 0;
    }
}