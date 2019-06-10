<?php
include "../vendor/autoload.php";
    $message = \Swift_Message::newInstance ()->setSubject ( 'Test Invio' )->setTo( 'francesco@desegno.it' )->setFrom ( array (
        'registrazioni@gustourconad.it' => 'Gustour Conad'
    ) )->setBody ( '<h1>Test</h1>', 'text/html' );

    $transport = \Swift_SmtpTransport::newInstance(
        'mail.gustourconad.it',
        25,
        'tls'
    )->setUsername('registrazioni@gustourconad.it')->setPassword('cnhuA4ZV_9');

    $transport->setStreamOptions([
        'ssl' => ['allow_self_signed' => true, 'verify_peer' => false, 'verify_peer_name' => false]
    ]);

    $mailer = \Swift_Mailer::newInstance ( $transport );
    var_dump($mailer->send ( $message ));
?>