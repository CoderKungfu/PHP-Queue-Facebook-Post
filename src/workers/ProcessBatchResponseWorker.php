<?php
class ProcessBatchResponseWorker extends PHPQueue\Worker
{
    private $resultLog;
    /**
     * @var PHPQueue\Backend\CSV
     */
    private $error_users;

    public function __construct()
    {
        parent::__construct();
        $this->resultLog = \PHPQueue\Logger::createLogger(
            'ResponseErrorLogger'
            , PHPQueue\Logger::INFO
            , dirname(__DIR__) . '/logs/response_error.log'
        );

        $filename = dirname(__DIR__) . '/logs/response_error_users.csv';
        if (!is_file($filename))
        {
            file_put_contents($filename, '');
        }
        $config = array('filePath'=>$filename);
        $this->error_users = \PHPQueue\Base::backendFactory('CSV', $config);
    }

    public function runJob($jobObject)
    {
        parent::runJob($jobObject);
        $jobData = $jobObject->data;
        if (!empty($jobData['batch_response']))
        {
            $batch_response = $jobData['batch_response'];
            $recipients = $jobData['recipients'];
            $rows = count($recipients);
            for($i=0; $i<$rows; $i++)
            {
                $response = $batch_response[$i];
                $recipient = $recipients[$i];
                if ($response['code'] != 200)
                {
                    $msg = sprintf("Error Code: %s - Unable to send to user %s (%s)",
                                      $response['code']
                                    , $recipient['name']
                                    , $recipient['fbid']
                                );
                    $this->resultLog->addError($msg, json_decode($response['body'], true));

                    $csv_log = array_values($recipient);
                    array_push($csv_log, $response['body']);
                    $this->error_users->add($csv_log);
                }
            }
        }
        $this->result_data = $jobData;
        return true;
    }
}
