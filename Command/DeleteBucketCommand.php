<?php
namespace Kbrw\RiakBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class DeleteBucketCommand extends ContainerAwareCommand
{

    const OPTION_BUCKET   = "bucket";
    const OPTION_CLUSTER  = "cluster";
    const OPTION_NO_CHECK = "yes";

    protected function configure()
    {
        $this
            ->setName('riak:bucket:deleteAll')
            ->setDescription('Delete all datas inside a bucket')
            ->addOption(self::OPTION_BUCKET,   "b", InputOption::VALUE_REQUIRED, "Bucket name")
            ->addOption(self::OPTION_CLUSTER,  "c", InputOption::VALUE_REQUIRED, "Cluster name")
            ->addOption(self::OPTION_NO_CHECK, "y", InputOption::VALUE_NONE,     "Automatically answer to all questions. Be aware that bucket will be deleted without the option to stop the command.")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');

        $bucketName = $input->getOption(self::OPTION_BUCKET);
        while (empty($bucketName)) {
            $bucketName = $dialog->ask($output, 'Name of the bucket you want to delete : ', null);
        }
        $clusterName = $input->getOption(self::OPTION_CLUSTER);
        while (empty($clusterName)) {
            $clusterName = $dialog->ask($output, 'Cluster containing the bucket you want to delete : ', null);
        }
        if (!$this->getContainer()->has("riak.cluster.$clusterName")) {
            $output->writeln("<error>Cluster '$clusterName' does not exist.</error>");

            return 1;
        }
        $cluster = $this->getContainer()->get("riak.cluster.$clusterName");
        if ((!$input->hasOption(self::OPTION_NO_CHECK) || !$input->getOption(self::OPTION_NO_CHECK)) && !$dialog->askConfirmation($output, '<question>Are you sure you want to delete this bucket ? Last chance before data are erased permanently. [y|n] : </question>', false)) {
            return 0;
        }
        $bucket = $cluster->getBucket($bucketName);
        $keys = $bucket->keys();
        if (!$bucket->delete($keys)) {
            $output->writeln("<error>Unable to delete all " . count($keys) . " keys from '$bucketName' bucket. Some keys might have been deleted though.</error>");

            return 2;
        }
        $output->writeln("<info>" . count($keys) . " keys have been deleted in '$bucketName' bucket.</info>");

        return 0;
    }
}
