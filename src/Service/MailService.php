<?php 

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class MailService {

    private $mailer;

    public function __construct(MailerInterface $mailer) {
        $this->mailer = $mailer;
    }

    public function sendMail($data, $to, $subject, $template) {

        // Envoie de mail
        $email = (new TemplatedEmail())
        ->from('contact@academiews.fr')
        ->to(new Address($to))
        ->subject($subject)
        // path of the Twig template to render
        ->htmlTemplate($template)
        ->context($data);

        $this->mailer->send($email);

    }

}


?>