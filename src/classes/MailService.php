<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService {
    private $mail;

    public function __construct($host, $username, $password, $fromEmail, $fromName, $port) {
        $this->mail = new PHPMailer(true);

        try {
            $this->mail->isSMTP();
            $this->mail->Host = $host;
            $this->mail->SMTPAuth = true;
            $this->mail->Username = $username;
            $this->mail->Password = $password;
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port = $port;
            $this->mail->setFrom($fromEmail, $fromName);
        } catch (Exception $e) {
            throw new Exception("Mailer Error: " . $this->mail->ErrorInfo);
        }
    }

    public function sendMail($toEmail, $subject, $message) {
        try {
            $this->mail->addAddress($toEmail);
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body = $message;
            $this->mail->AltBody = strip_tags($message);

            $this->mail->send();
        } catch (Exception $e) {
            throw new Exception("Mailer Error: " . $this->mail->ErrorInfo);
        }
    }
}
?>