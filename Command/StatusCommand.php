<?php
namespace Kbrw\RiakBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class StatusCommand extends ContainerAwareCommand
{

    const OPTION_CLUSTER = "cluster";
    const OPTION_KEY     = "key";

    protected function configure()
    {
        $this
            ->setName('riak:cluster:status')
            ->setDescription('List all status informations on a cluster. You can also display a single status key.')
            ->addOption(self::OPTION_CLUSTER, "c", InputOption::VALUE_REQUIRED, "Cluster name")
            ->addOption(self::OPTION_KEY,     "k", InputOption::VALUE_OPTIONAL, "The key you want to display. If not provided, all keys will be displayed.")
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
        $key = $input->getOption(self::OPTION_KEY);
        if (empty($key)) {
            foreach ($status as $key => $value) {
               $this->displayStatusInformation($key, $value, $output);
            }
        }
        else if (array_key_exists($key, $status)) {
            $this->displayStatusInformation($key, $status->{$key}, $output);
        }
        else {
            $output->writeln("<error>Not Found Key</error> : Key '$key' not found.");
        }
        
        return 0;
    }
    
    protected function displayStatusInformation($key, $value, $output)
    {
        if (!empty($key) && is_array($value)) {
            $output->writeln("$key : ");
            foreach($value as $subvalue) {
                $output->writeln(" - '$subvalue'");
            }
        }
        else {
            $output->writeln("$key : '" . $value . "'");
        }
    }
}
