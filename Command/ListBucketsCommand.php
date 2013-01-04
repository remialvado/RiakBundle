<?php
namespace Kbrw\RiakBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class ListBucketsCommand extends ContainerAwareCommand
{

    const OPTION_CLUSTER  = "cluster";
    const OPTION_RAW     = "raw";

    protected function configure()
    {
        $this
            ->setName('riak:cluster:list')
            ->setDescription('List all buckets inside a bucket.')
            ->addOption(self::OPTION_CLUSTER, "c", InputOption::VALUE_REQUIRED, "Cluster name")
            ->addOption(self::OPTION_RAW,     "r", InputOption::VALUE_NONE,     "Display buckets without style.")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');

        $clusterName = $input->getOption(self::OPTION_CLUSTER);
        while (empty($clusterName)) {
            $clusterName = $dialog->ask($output, 'Cluster name : ', null);
        }
        if (!$this->getContainer()->has("riak.cluster.$clusterName")) {
            $output->writeln("<error>Cluster '$clusterName' does not exist.</error>");

            return 1;
        }
        $bucketNames = $this->getContainer()->get("riak.cluster.$clusterName")->bucketNames();
        if ($input->hasOption(self::OPTION_RAW) && $input->getOption(self::OPTION_RAW)) {
            echo implode(" ", $bucketNames);
        } elseif (count($bucketNames) > 0) {
            $output->writeln("<info>Bucket(s) in '$clusterName' cluster : </info>");
            echo " - " . implode("\n - ", $bucketNames) . "\n";
        } else {
            $output->writeln("<error>Cluster '$clusterName' is empty. </error>");
        }

        return 0;
    }
}
