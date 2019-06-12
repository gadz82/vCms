<?php

namespace apps\admin\library;

use Phalcon\Mvc\User\Component;
use Phalcon\Mvc\View;

require_once(APP_DIR . '/library/swift/swift_required.php');

/**
 * Sends e-mails based on pre-defined templates
 */
class Mailer extends Component
{
    protected $_transport;

    /**
     * Sends e-mails via gmail based on predefined templates
     *
     * @param array $to
     * @param string $subject
     * @param string $name
     * @param array $params
     */
    public function send($to, $subject, $name, $params, $attachments = [])
    {
        // Settings
        $mailSettings = $this->config->mailer;

        $template = $this->getTemplate($name, $params);

        // Create the message
        $message = \Swift_Message::newInstance()->setSubject($subject)->setTo($to)->setFrom([
            $mailSettings->fromEmail => $mailSettings->fromName
        ])->setBody($template, 'text/html');

        if (!empty ($attachments)) {
            $count = count($attachments);
            for ($i = 0; $i < $count; $i++) {
                if ($attachments [$i] ['type'] == 'standard') {
                    $message->attach(\Swift_Attachment::fromPath($attachments [$i] ['path']));
                } else {
                    $message->attach(\Swift_Attachment::newInstance($attachments [$i] ['data'], $attachments [$i] ['filename'], 'application/pdf'));
                }
            }
        }

        if (!$this->_transport) {
            if ($mailSettings->method == 'smtp') {
                $this->_transport = \Swift_SmtpTransport::newInstance(
                    $mailSettings->smtp->server,
                    $mailSettings->smtp->port,
                    $mailSettings->smtp->security
                )->setUsername($mailSettings->smtp->username)->setPassword($mailSettings->smtp->password);
            } else {
                $this->_transport = \Swift_MailTransport::newInstance();
            }
        }

        // Create the Mailer using your created Transport
        $mailer = \Swift_Mailer::newInstance($this->_transport);
        return $mailer->send($message);
    }

    /**
     * Applies a template to be used in the e-mail
     *
     * @param string $name
     * @param array $params
     */
    public function getTemplate($name, $params)
    {

        // $parameters = array_merge(array('publicUrl'=>$this->config->application->publicUrl), $params);
        $parameters = array_merge([], $params);

        return $this->view->getRender('emailTemplates', $name, $parameters, function ($view) {
            $view->setRenderLevel(View::LEVEL_LAYOUT);
        });

        return $view->getContent();
    }
}