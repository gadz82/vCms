<?php

namespace apps\api\library;

use Phalcon\Mvc\User\Component;
use Phalcon\Mvc\View;

/**
 * Sends e-mails based on pre-defined templates
 */
class Mailer extends Component {
	protected $_transport;

    /**
     * @param $to
     * @param array $cc
     * @param array $bcc
     * @param $subject
     * @param $name
     * @param $params
     * @param array $attachments
     * @return int
     */
	public function send($to, $cc = [], $bcc = [], $subject, $name, $params, $attachments = array()) {
		// Settings
		$mailSettings = $this->config->mailer;

		$template = $this->getTemplate ( $name, $params );

		// Create the message
		$message = \Swift_Message::newInstance ()->setSubject ( $subject )->setTo( $to )->setFrom ( array (
				$mailSettings->fromEmail => $mailSettings->fromName
		) )->setBody ( $template, 'text/html' );

        if(!empty($cc)) $message->setCc($bcc);
        if(!empty($bcc)) $message->setBcc($bcc);

		if (! empty ( $attachments )) {
			$count = count ( $attachments );
			for($i = 0; $i < $count; $i ++) {
				if ($attachments [$i] ['type'] == 'standard') {
					$message->attach ( \Swift_Attachment::fromPath ( $attachments [$i] ['path'] ) );
				} else {
					$message->attach ( \Swift_Attachment::newInstance ( $attachments [$i] ['data'], $attachments [$i] ['filename'], 'application/pdf' ) );
				}
			}
		}

        if (! $this->_transport) {
            if($mailSettings->method == 'smtp'){
                $this->_transport = \Swift_SmtpTransport::newInstance(
                    $mailSettings->smtp->server,
                    $mailSettings->smtp->port,
                    $mailSettings->smtp->security
                )->setUsername($mailSettings->smtp->username)->setPassword($mailSettings->smtp->password);
            } else {
                $this->_transport = \Swift_MailTransport::newInstance ();
            }
        }

		// Create the Mailer using your created Transport
		$mailer = \Swift_Mailer::newInstance ( $this->_transport );

		return $mailer->send ( $message );
	}

    /**
     * @param $name
     * @param $params
     * @return string
     */
	public function getTemplate($name, $params) {

        $tpl = 'default';
        $this->view->setViewsDir("../apps/api/views/");
        if($this->view->exists('partials/forms-email/'.$name)){
            $tpl = $name;
        }

		return $this->view->getRender ( 'forms-email', $tpl, ['params' => $params], function ($view) {
            $view->setViewsDir("../apps/api/views/partials/");
            $view->setRenderLevel ( View::LEVEL_LAYOUT );
		});
	}
}