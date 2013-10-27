<?php
echo 'Test';
echo Zend_Version::VERSION;
echo 'no version?';
exit;
require_once 'Zend\Mail\Message.php';
require_once 'Zend\Mail\Transport\Smtp.php';
require_once 'Zend\Mail\Transport\SmtpOptions.php';

$message = new Message();
echo 'create msg<br>';
$message->addTo('mrmchase08@gmail.com')
        ->addFrom('reallistic@plexcloud.tv')
        ->setSubject('Greetings and Salutations!')
        ->setBody("Sorry, I'm going to be late today!");

// Setup SMTP transport using LOGIN authentication
$transport = new Smtp();
echo 'create transport<br>';
$options   = new SmtpOptions(array(
    'name'              => 'smtp.plexcloud.tv',
    'host'              => 'smtp.plexcloud.tv',
    'connection_class'  => 'login',
    'connection_config' => array(
        'username' => 'reallistic',
        'password' => 'the17nsu',
    ),
));
echo 'create opt<br>';
$transport->setOptions($options);
$transport->send($message);

?>