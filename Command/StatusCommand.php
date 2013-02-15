<?php
namespace Kbrw\RiakBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class StatusCommand extends ContainerAwareCommand
{

    const OPTION_CLUSTER = "cluster";

    protected function configure()
    {
        $this
            ->setName('riak:cluster:status')
            ->setDescription('Get status.')
            ->addOption(self::OPTION_CLUSTER, "c", InputOption::VALUE_REQUIRED, "Cluster name")
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
        $status = $this->getContainer()->get("riak.cluster.$clusterName")->status();
        $output->writeln("<info>Status for '$clusterName' cluster : </info>");
        foreach ($status as $k => $v) {
            echo " - $k: $v\n";
        }

        return 0;
    }
}
