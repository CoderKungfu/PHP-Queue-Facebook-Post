<?php
/**
 * Created own Facebook from BaseFacebook abstract class as we don't need to have persistent data.
 */
class MyFacebook extends BaseFacebook
{
    protected function setPersistentData($key, $value){}
    protected function getPersistentData($key, $default = false){}
    protected function clearPersistentData($key){}
    protected function clearAllPersistentData(){}
}

class PublishFBFeedWorker extends PHPQueue\Worker
{
    /**
     * @var MyFacebook
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
        $this->facebook = new MyFacebook($config);
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
