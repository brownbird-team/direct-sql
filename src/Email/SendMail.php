<?php

namespace Email;

use PHPMailer\PHPMailer\PHPMailer;

class SendMail {
    private $mail;

    public function __construct($subject, $recipient, $message = '') {
        $this->mail = new PHPMailer();

        require_once __DIR__ . '/../../config/config.php';

        $this->mail->isSMTP();
        $this->mail->Host = $config['smtp_hostname'];
        $this->mail->Port = $config['smtp_port'];
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $config['smtp_username'];
        $this->mail->Password = $config['smtp_password'];

        $this->mail->setFrom($config['smtp_username'], $config['email_from']);

        $this->mail->addAddress($recipient);
        $this->mail->Subject = $subject;

        $this->mail->Body = $message;
    }

    public function add_html($html) {
        $this->mail->AltBody = $this->mail->Body;
        $this->mail->isHTML();
        $this->mail->Body = $html;
    }

    public function send() {
        return $this->mail->send();
    }
}