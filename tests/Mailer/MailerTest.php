<?php

namespace App\Tests\Mailer;

use App\Entity\User;
use App\Mailer\Mailer;
use PHPUnit\Framework\TestCase;

class MailerTest extends TestCase {

    public function testConfirmationEmail() {

        $user = new User();
        $user->setEmail("test@tes.com");

        // MockBuilder
        // Los MockBuilder es un metodo del phpUnit que nos permite crear instancias de la class que pasamos
        // para asi utilizarlas como injections en las class que necesitamos para testear.
        // El metodo disableOriginalConstructor() se utiliza por si la class en el constructor recibe algun parametro, de esta manera
        // no necesitamos nada, solo la class.
        // Mock del SwiftMailer
        $swiftMailer = $this->getMockBuilder(\Swift_Mailer::class)->disableOriginalConstructor()->getMock();

        $swiftMailer->expects($this->once())->method("send")
                ->with($this->callback(function ($subject) {

                            // hacer un cast del parametro a string
                            $messageStr = (string) $subject;

                            // Ir comprobando si la variable contiene el cuerpo del correo que hemos definido
                            return strpos($messageStr, "From: phpunit@test.com") != false 
                                && strpos($messageStr, "Content-Type: text/html; charset:utf-8") != false
                                && strpos($messageStr, "Subject: Welcome to the micro-post app!") != false
                                && strpos($messageStr, "To: test@tes.com") != false
                                && strpos($messageStr, "Message Body") != false;
                        }));

        // Mock del Twig_Enviroment
        $twigEnviroment = $this->getMockBuilder(\Twig_Environment::class)->disableOriginalConstructor()->getMock();

        // En la class Mailer, la instancia twig llama al metodo render y con los parametros del template y el usuario
        // Para "emular" eso, se llama al metodo expects(se define $this->once) y seguido el metodo con el nombre, en este caso "render"
        // Y los parametros que me pasamos al render.
        $twigEnviroment->expects($this->once())
                ->method("render")
                ->with("email/registration.html.twig", ["user" => $user])
                ->willReturn("Message Body"); // el metodo render de del twig devuelve un texto, lo definimos aqui con el willReturn

        $mailer = new Mailer($swiftMailer, $twigEnviroment, "phpunit@test.com");
        $mailer->sendConfirmationEmail($user);
    }

}
