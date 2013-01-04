<?php
namespace Kbrw\RiakBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RiakTestCommand extends ContainerAwareCommand
{

    /**
     * @var \Kbrw\RiakBundle\Model\Cluster\Cluster
     */
    protected $cluster;

    protected function configure()
    {
        $this
            ->setName('riak:test')
            ->setDescription('Debug stuffs. Used for test only.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->cluster = $this->getContainer()->get("riak.cluster.backend");
        //$this->search();
        $this->mapreduce();
    }

    protected function injectData()
    {
        $bucket = $this->cluster->getBucket("test_users", true);
        $bucket->delete($bucket->keys());
        $bucket->put(array("remi1" => new \Acme\DemoBundle\Model\User("remi1", "remi.alvado" . rand(1, 10000) . "@gmail.com")));
        $bucket->put(array("remi2" => new \Acme\DemoBundle\Model\User("remi2", "remi.alvado" . rand(1, 10000) . "@gmail.com")));
    }

    protected function search()
    {
        $this->injectData();
        $bucket = $this->cluster->getBucket("test_users", true);
        print_r($bucket->sasearch(new \Kbrw\RiakBundle\Model\Search\Query("id:rem*", "id")));
    }

    protected function mapreduce()
    {
        $bucket = $this->cluster->getBucket("meals", true);
        $bucket->delete($bucket->keys());
        $bucket->put(array("summer-1" => "pizza data goes here"));
        $bucket->put(array("summer-2" => "pizza pizza pizza pizza"));
        $bucket->put(array("winter-1" => "nothing to see here"));
        $bucket->put(array("autumn-1" => "pizza pizza pizza"));
        $result = $this->cluster->mapReduce()
          ->on("test_training")
          ->map('
              function(riakObject) {
                  var m =  riakObject.values[0].data.match("pizza");

                  return  [[riakObject.key, (m ? m.length : 0 )]];
              }
          ')
          ->send();
        var_dump($result);
    }
}
