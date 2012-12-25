<?php
class ProcessBatchResponseWorkerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ProcessBatchResponseWorker
     */
    private $object;
    private $csv_file;
    private $log_file;

    public function setUp()
    {
        parent::setUp();

        $this->csv_file = dirname(dirname(__DIR__)) . '/src/logs/response_error_users.csv';
        if (is_file($this->csv_file)) unlink($this->csv_file);

        $this->log_file = dirname(dirname(__DIR__)) . '/src/logs/response_error.log';
        if (is_file($this->log_file)) unlink($this->log_file);
        clearstatcache();

        $this->object = \PHPQueue\Base::getWorker('ProcessBatchResponse');
    }

    public function testRunJob()
    {
        $data1 = array(
            'worker' => 'PreparePayload',
            'data'   => array(
                'recipients'     => array(
                        array('fbid'=>12345, 'name'=>'Mohd Noor'),
                        array('fbid'=>678910, 'name'=>'Rafik Shaifiq')
                    ),
                'message'        => "Quick brown fox jumped over the moon.",
                'message_body'   => array(
                    'link'			=> 'http://www.facebook.com/islamicevents.sg',
                    'picture'		=> 'http://www.islamicsgnetworks.com/sgprayertime/5prayers/iloveAllah.jpg',
                    'name'			=> 'www.islamicevents.sg',
                    'caption'		=> 'A one stop portal for Community, Sharing, Trusts & Innovation',
                    'description'	=> 'Alhamdulillah. With your support we have grown to more than 9,000 followers. Be part of this family so that you can be updated on the latest Islamic events, courses, news and more in Singapore. Like our FB Page.',
                    'message'        => "Quick brown fox jumped over the moon."
                ),
                'batch_response' => array(
                        array('code'=>200,'body'=>'{"status":"OK"}'),
                        array('code'=>500,'body'=>'{"error":{"type":"OAuthException"}}')
                    )
            )
        );
        $job = new \PHPQueue\Job($data1);
        $this->object->runJob($job);
        $this->assertTrue(is_file($this->log_file));
        $this->assertTrue(is_file($this->csv_file));
    }
}
