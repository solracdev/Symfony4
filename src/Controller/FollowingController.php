<?php

namespace App\Controller;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/following")
 * @Security("is_granted('ROLE_USER')", message="Access denied")
 */
class FollowingController extends AbstractController {

    /**
     * @Route("/follow/{id}", name="follow_user")
     */
    public function follow(User $follow_user) {
        
        // Instancia del usuario que esta logeado en la APP
        $currentUser = $this->getUser();
        
        // Comprobamos que el usuario pasado por parametro y el logeado sean diferentes
        if ($follow_user->getId() !== $currentUser->getId()) {

            // Llamar al metodo de la entidad User que comprobara si el usuario no esta ya en al collection y de ser asi lo añadira.
            $currentUser->follow($follow_user);

            // Actualizamos la BBDD con el metodo flush del entityManager
            $this->getDoctrine()->getManager()->flush();
        }

        // Redireccionamos a la ruta micro_post_user con el parametro user recibido
        return $this->redirectToRoute("micro_post_user", ["username" => $follow_user->getUsername()]);
    }

    /**
     * @Route("/unfollow/{id}", name="unfollow_user")
     */
    public function unfollow(User $unfollow_user) {

        $currentUser = $this->getUser();

        $currentUser->getFollowing()->removeElement($unfollow_user);

        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute("micro_post_user", ["username" => $unfollow_user->getUsername()]);
    }

}
