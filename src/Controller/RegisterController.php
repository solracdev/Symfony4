<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\Register\UserRegisterEvent;
use App\Form\UserType;
use App\Security\TokenGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController {

    /**
     * @Route("/register", name="user_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEnconder, EventDispatcherInterface $eventDispatcher, TokenGenerator $tokenGenerator) {

        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Crear el password codificado
            // hay dos maneras de coger los campos del formulario
            // 1) usando los getters de la misma class
            // 2) usando el form->get("key del campo")->getData()
            $password = $passwordEnconder->encodePassword($user, $user->getPlainPassword());

            // establecer el password al user con el password del formulario codificado
            $user->setPassword($password);
            
            // establecer el token, llamando al metodo de la class token generator, con un length de 30 caracteres
            $user->setConfirmationToken($tokenGenerator->getSecureToken(30));

            // EntityManager
            $em = $this->getDoctrine()->getManager();

            // Persistir la entidad
            $em->persist($user);
            $em->flush();
            
            // Enviar un evento cuando se registra un usuario
            
            // Instancia de UserRegisterEvent con la instancai del usuario registrado como parametro
            $userRegisterEvent = new UserRegisterEvent($user);
            
            // Enviar el evento de la class UserRegisterEvent, junto con la instancia
            $eventDispatcher->dispatch(UserRegisterEvent::NAME, $userRegisterEvent);

            // Redirecionar al index
            return $this->redirectToRoute("security_login");
        }

        // Cargar el template de registro con la variable form, que su valor sera la vista del formulario
        return $this->render("register/register.html.twig", ["form" => $form->createView()]);
    }

}
