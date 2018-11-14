<?php

namespace App\Mailer;

use App\Entity\User;

class Mailer {

    /**
     * @var string
     */
    private $emailFrom;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, string $emailFrom) {

        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->emailFrom = $emailFrom;
    }

    public function sendConfirmationEmail(User $user) {

        // Cargar la plantilla twig que pasaremos al mensaje, con el parametro del usuario que tenemos en el event
        $body = $this->twig->render("email/registration.html.twig", ["user" => $user]);

        // Instancia de Swift_Message
        // En php si rodeamos el new entre parentesis podemos usar los seters para cuando creamos la instancia
        $message = (new \Swift_Message())
                ->setSubject("Welcome to the micro-post app!")
                ->setFrom($this->emailFrom)
                ->setTo($user->getEmail())
                ->setBody($body, "text/html");


        // Enviar el mensaje con el metodo del swift_mailer
        $this->mailer->send($message);
    }

}
