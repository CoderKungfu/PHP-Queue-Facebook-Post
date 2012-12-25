<?php
class PreparePayloadWorker extends PHPQueue\Worker
{
    /**
     * @param \PHPQueue\Job $jobObject
     */
    public function runJob($jobObject)
    {
        parent::runJob($jobObject);
        $jobData = $jobObject->data;
        $payload = array();
        if (empty($jobData['message_body']))
        {
            throw new \PHPQueue\Exception\BackendException('Message body not specified.');
        }
        foreach($jobData['recipients'] as $recipient)
        {
            if (!empty($recipient['fbid']))
            {
                $payload[] = array(
                    'method'       => 'POST',
                    'relative_url' => sprintf('/%s/feed', $recipient['fbid']),
                    'body'         => http_build_query($jobData['message_body'])
                );
            }
        }
        $jobData['batch_payload'] = $payload;
        $this->result_data = $jobData;
        return true;
    }
}
