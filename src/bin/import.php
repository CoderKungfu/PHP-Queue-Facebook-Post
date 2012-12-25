#!/usr/bin/php
<?php
// Usage:
// $ php import.php 'The user message'

if (is_file(__DIR__ . '/env.php'))
{
    require_once __DIR__ . '/env.php';
}
require_once dirname(__DIR__) . '/config.php';

$debug = (bool)getenv('fb_debug');

Clio\Console::output('Starting Import... ');

$message = $argv[1];
Clio\Console::output('Message: '.$message);

$pdo_string = getenv('pdo_string');
$db_user = getenv('db_user');
$db_password = getenv('db_password');

$count_sql = 'SELECT count(*) as TotalRcpt FROM AppUsers WHERE `Active`=1';
$select_sql = 'SELECT `fbid`, `name` FROM AppUsers WHERE `Active`=1 ORDER BY `id` ASC LIMIT ?, ?';

try
{
    Clio\Console::stdout('Connecting to DB...');
    $dbh = new PDO($pdo_string, $db_user, $db_password);
    Clio\Console::output('%g[OK]%n');

    // Count Total Recipients
    $count_query = $dbh->query($count_sql);
    $row = $count_query->fetch(PDO::FETCH_ASSOC);
    $total_recipients = $row['TotalRcpt'];
    Clio\Console::output('Total Recipients: %_'.$total_recipients . '%n');

    // Prepare Statement
    $sth = $dbh->prepare($select_sql);

    $offset = 0;
    $batch_size = 50;
    while($offset < $total_recipients)
    {
        $payload = array(
            'message' => $message,
            'recipients' => array()
        );
        Clio\Console::output(sprintf('Fetching %s from: %%_%s%%n', $batch_size, $offset));
        $sth->bindParam(1, $offset, PDO::PARAM_INT);
        $sth->bindParam(2, $batch_size, PDO::PARAM_INT);
        $sth->execute();
        $payload['recipients'] = $sth->fetchAll(PDO::FETCH_ASSOC);

        print_r($payload);

        Clio\Console::stdout('Adding new Job to Queue...');
        if (!$debug)
        {
            $queue = \PHPQueue\Base::getQueue('FBPost');
            \PHPQueue\Base::addJob($queue, $payload);
            Clio\Console::output('%g[OK]%n');
        }
        else
        {
            Clio\Console::output('%C[SKIPPED]%n');
        }
        $offset += $batch_size;
    }
}
catch (Exception $e)
{
    Clio\Console::output('%r[FAILED]%n');
    Clio\Console::output('%rError:%n %_'. $e->getMessage() . '%n');
    die();
}
Clio\Console::output('%gDone!%n');
