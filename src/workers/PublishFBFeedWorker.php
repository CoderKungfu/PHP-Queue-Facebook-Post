<?php
class PublishFBFeedWorker extends PHPQueue\Worker
{
    /**
     * @var Facebook
     */
    private $facebook;
    private $token;

    public function __construct()
    {
        parent::__construct();

        $config = array(
                  'appId' => getenv('fb_app_id')
                , 'secret' => getenv('fb_app_secret')
            );
        $this->facebook = new Facebook($config);
        $this->token = $this->facebook->getAccessToken();
    }

    public function runJob($jobObject)
    {
        parent::runJob($jobObject);
        $jobData = $jobObject->data;
        if (empty($jobData['batch_payload']))
        {
            throw new \PHPQueue\Exception\BackendException('No batch payload specified.');
        }
        $encoded_payload = urlencode(json_encode($jobData['batch_payload']));
        $api_cmd = sprintf('?access_token=%s&batch=%s', $this->token, $encoded_payload);
        $jobData['batch_response'] = $this->facebook->api($api_cmd, 'POST');
        $this->result_data = $jobData;
        return true;
    }
}
