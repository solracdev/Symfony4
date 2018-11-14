<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController {

    /**
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $authenticacionUtils) {

        return $this->render("security/login.html.twig",
                        [
                            "last_username" => $authenticacionUtils->getLastUsername(),
                            "error" => $authenticacionUtils->getLastAuthenticationError()
        ]);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout() {
        
    }

    /**
     * @Route("/confirm/{token}", name="security_confirm")
     */
    public function confirm(string $token) {

        // Buscar el usuario por el token recibido por parametro
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(["confirmationToken" => $token]);

        // Comprobar si se ha encontado un usuario con ese token
        if (null !== $user) {

            // Activar el usuario
            $user->setEnabled(true);

            // Resetear el token
            $user->setConfirmationToken("");

            // Guardar los cambios en la BBDD
            $this->getDoctrine()->getManager()->flush();
        }

        // Generar el template para confirmar el usuario, con el usuario encontrado con el token
        return $this->render("security/confirmation.html.twig", ["user" => $user]);
    }

}
