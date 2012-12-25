<?php
class FBPostQueue extends PHPQueue\JobQueue
{
    /**
     * @var PHPQueue\Backend\Beanstalkd
     */
    private $dataSource;
    /**
     * @var PHPQueue\Backend\MongoDB
     */
    private $recipientSource;
    private $queueWorker = array('PreparePayload', 'PublishFBFeed', 'ProcessBatchResponse');
    private $resultLog;
    private $message_body = array(
            'link'			=> 'https://github.com/miccheng/php-queue',
            'picture'		=> 'http://www.gravatar.com/avatar/cad06c496d78923202f2c30444fab2ea.png',
            'name'			=> 'PHP-Queue',
            'caption'		=> 'Hosted on Github',
            'description'	=> 'A unified front-end for different queuing backends. Includes a REST server, CLI interface and daemon runners.'
        );

    public function __construct()
    {
        parent::__construct();
        $type = getenv('backend_target') ? getenv('backend_target') : 'MainQueue';
        $config = FBPostConfig::getConfig($type);
        $this->dataSource = \PHPQueue\Base::backendFactory($config['backend'], $config);

        $type = getenv('recipient_store') ? getenv('recipient_store') : 'RecipientStore';
        $config = FBPostConfig::getConfig($type);
        $this->recipientSource = \PHPQueue\Base::backendFactory($config['backend'], $config);

        $this->resultLog = \PHPQueue\Logger::createLogger(
            'MainLogger'
            , PHPQueue\Logger::INFO
            , dirname(__DIR__) . '/logs/main.log'
        );
    }

    public function addJob(array $newJob)
    {
        if (empty($newJob['recipients']) && is_array($newJob['recipients']))
        {
            throw new \PHPQueue\Exception\Exception('No recipients.');
        }
        $newJob['batch_key'] = $this->genBatchKey();
        $this->recipientSource->add($newJob['recipients'], $newJob['batch_key']);
        unset($newJob['recipients']);

        $formatted_data = array('worker'=>$this->queueWorker, 'data'=>$newJob);
        $this->dataSource->add($formatted_data);
        $this->resultLog->addInfo('Adding new job: ', $newJob);
        return true;
    }

    public function getJob()
    {
        $job_data = $this->dataSource->get();
        $data = $job_data['data'];
        if (empty($data['batch_key']))
        {
            throw new \PHPQueue\Exception\BackendException('No batch key specified.');
        }
        $data['recipients'] = $this->recipientSource->get($data['batch_key']);
        $data['message_body'] = array_merge($this->message_body, array('message'=>$data['message']));
        $job_data['data'] = $data;
        $nextJob = new \PHPQueue\Job($job_data, $this->dataSource->last_job_id);
        $this->last_job_id = $this->dataSource->last_job_id;
        return $nextJob;
    }

    public function updateJob($jobId = null, $resultData = null)
    {
        $this->resultLog->addInfo('Result: ID='.$jobId, $resultData);
    }

    public function clearJob($jobId = null)
    {
        $this->dataSource->clear($jobId);
    }

    public function releaseJob($jobId = null)
    {
        $this->dataSource->release($jobId);
    }

    private function genBatchKey()
    {
        $seed = sprintf('%s-%s', uniqid(), time());
        return md5($seed);
    }
}
