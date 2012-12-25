<?php
class PreparePayloadWorkerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PreparePayloadWorker
     */
    private $object;

    public function setUp()
    {
        parent::setUp();
        $this->object = \PHPQueue\Base::getWorker('PreparePayload');
    }

    public function testRunJob()
    {
        $data1 = array(
            'worker' => 'PreparePayload',
            'data'   => array(
                'recipients' => array(
                      array('fbid'=>12345, 'name'=>'Mohd Noor')
                    , array('fbid'=>678910, 'name'=>'Rafik Shaifiq')
                ),
                'message' => "Quick brown fox jumped over the moon.",
                'message_body'   => array(
                    'link'			=> 'http://www.facebook.com/islamicevents.sg',
                    'picture'		=> 'http://www.islamicsgnetworks.com/sgprayertime/5prayers/iloveAllah.jpg',
                    'name'			=> 'www.islamicevents.sg',
                    'caption'		=> 'A one stop portal for Community, Sharing, Trusts & Innovation',
                    'description'	=> 'Alhamdulillah. With your support we have grown to more than 9,000 followers. Be part of this family so that you can be updated on the latest Islamic events, courses, news and more in Singapore. Like our FB Page.',
                    'message'        => "Quick brown fox jumped over the moon."
                )
            )
        );
        $job = new \PHPQueue\Job($data1);
        $this->object->runJob($job);
        $this->assertTrue(isset($this->object->result_data['batch_payload']));
        $this->assertEquals(2,count($this->object->result_data['batch_payload']));

        $recipient1 = $this->object->result_data['batch_payload'][0];
        $this->assertEquals('/12345/feed', $recipient1['relative_url']);
        $this->assertGreaterThan(0, strpos($recipient1['body'], "Quick+brown+fox+jumped+over+the+moon."));
    }
}
