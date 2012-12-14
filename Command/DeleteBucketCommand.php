<?php
namespace Kbrw\RiakBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class RiakDebugCommand extends ContainerAwareCommand
{
    
    const OPTION_BUCKET   = "bucket";
    const OPTION_CLUSTER  = "cluster";
    const OPTION_NO_CHECK = "yes";
    
    protected function configure()
    {
        $this
            ->setName('riak:delete')
            ->setDescription('Delete all datas inside a bucket')
            ->addOption(self::OPTION_BUCKET,   "b", InputOption::VALUE_REQUIRED, "Name of the bucket you want to delete.")
            ->addOption(self::OPTION_CLUSTER,  "c", InputOption::VALUE_REQUIRED, "Cluster containing the bucket you want to delete.")
            ->addOption(self::OPTION_NO_CHECK, "y", InputOption::VALUE_NONE,     "Automatically answer to all questions. Be aware that bucket will be deleted without the option to stop the command.")
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');
        
        $bucketName = $input->getOption(self::OPTION_BUCKET);
        while (empty($bucketName)) {
            $bucketName = $dialog->ask($output, 'Name of the bucket you want to delete', null);
        }
        $clusterName = $input->getOption("bucket");
        while (empty($clusterName)) {
            $clusterName = $dialog->ask($output, 'Cluster containing the bucket you want to delete', null);
        }
        if (!$this->getContainer()->has("riak.cluster.$clusterName")) {
            $output->writeln("<error>Cluster '$clusterName' does not exist.</error>");
            return 1;
        }
        $cluster = $this->getContainer()->get("riak.cluster.$clusterName");
        if (!$input->hasOption(self::OPTION_NO_CHECK && !$dialog->askConfirmation($output, '<question>Continue with this action?</question>', false))) {
            return 0;
        }
        $bucket = $cluster->getBucket($bucketName);
        return $bucket->delete($bucket->keys());
    }
}