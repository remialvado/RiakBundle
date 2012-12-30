<?php
namespace Kbrw\RiakBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class DeleteKeyCommand extends ContainerAwareCommand
{

    const OPTION_BUCKET   = "bucket";
    const OPTION_CLUSTER  = "cluster";
    const OPTION_KEY      = "key";
    const OPTION_NO_CHECK = "yes";

    protected function configure()
    {
        $this
            ->setName('riak:bucket:delete')
            ->setDescription('Delete one key inside a bucket')
            ->addOption(self::OPTION_BUCKET,   "b", InputOption::VALUE_REQUIRED, "Bucket name")
            ->addOption(self::OPTION_CLUSTER,  "c", InputOption::VALUE_REQUIRED, "Cluster name")
            ->addOption(self::OPTION_KEY,      "k", InputOption::VALUE_REQUIRED, "Key")
            ->addOption(self::OPTION_NO_CHECK, "y", InputOption::VALUE_NONE,     "Automatically answer to all questions. Be aware that bucket will be deleted without the option to stop the command.")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');

        // Get Key
        $key = $input->getOption(self::OPTION_KEY);
        while (empty($key)) {
            $key = $dialog->ask($output, 'Key : ', null);
        }

        // Get Bucket
        $bucketName = $input->getOption(self::OPTION_BUCKET);
        while (empty($bucketName)) {
            $bucketName = $dialog->ask($output, 'Bucket name : ', null);
        }

        // Get Cluster
        $clusterName = $input->getOption(self::OPTION_CLUSTER);
        while (empty($clusterName)) {
            $clusterName = $dialog->ask($output, 'Cluster name : ', null);
        }
        if (!$this->getContainer()->has("riak.cluster.$clusterName")) {
            $output->writeln("<error>Cluster '$clusterName' does not exist.</error>");

            return 1;
        }
        $cluster = $this->getContainer()->get("riak.cluster.$clusterName");
        if ((!$input->hasOption(self::OPTION_NO_CHECK) || !$input->getOption(self::OPTION_NO_CHECK)) && !$dialog->askConfirmation($output, '<question>Are you sure you want to delete this key ? Last chance before data are erased permanently. [y|n] : </question>', false)) {
            return 0;
        }
        $bucket = $cluster->getBucket($bucketName);
        if (!$bucket->delete($key)) {
            $output->writeln("<error>Unable to delete key '$key' in '$bucketName' bucket.</error>");

            return 2;
        }
        $output->writeln("<info>Key '$key' has been deleted in '$bucketName' bucket.</info>");

        return 0;
    }
}
