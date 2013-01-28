<?php
namespace Kbrw\RiakBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class DisableIndexingCommand extends ContainerAwareCommand
{

    const OPTION_BUCKET  = "bucket";
    const OPTION_CLUSTER = "cluster";
    const OPTION_NO_CHECK = "yes";

    protected function configure()
    {
        $this
            ->setName('riak:bucket:indexing:disable')
            ->setDescription('Deactivate indexing on a given bucket')
            ->addOption(self::OPTION_BUCKET,  "b", InputOption::VALUE_REQUIRED, "Bucket name")
            ->addOption(self::OPTION_CLUSTER, "c", InputOption::VALUE_REQUIRED, "Cluster name")
            ->addOption(self::OPTION_NO_CHECK, "y", InputOption::VALUE_NONE,     "Automatically answer to all questions. Be aware that indexing will be deactivated without the option to stop the command.")
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
        if ((!$input->hasOption(self::OPTION_NO_CHECK) || !$input->getOption(self::OPTION_NO_CHECK)) && !$dialog->askConfirmation($output, '<question>Are you sure you want to deactivate indexing ? Last chance [y|n] : </question>', false)) {
            return 0;
        }

        $cluster = $this->getContainer()->get("riak.cluster.backend");
        $bucket = $cluster->getBucket($bucketName);
        $bucket->disableSearchIndexing();
        $bucket->save();
        $output->writeln("<info>Indexing on '$bucketName' deactivated</info>");

        return 0;
    }
}
