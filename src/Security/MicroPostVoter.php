<?php

namespace App\Security;

use App\Entity\MicroPost;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MicroPostVoter extends Voter {

    const EDIT = 'edit';
    const DELETE = 'delete';
    
    /**
     * @var AccessDecisionManagerInterface
     */
    private $decisionManager;

    /**
     * Construct
     * @param AccessDecisionManagerInterface $decisionManager
     */
    public function __construct(AccessDecisionManagerInterface $decisionManager) {
        
        $this->decisionManager = $decisionManager;
    }
    
    /**
     * Comprobar los permisos cuando la funcion devuelve true
     * @param type $attribute accion que nos llega dese el twig / controller
     * @param type $subject instancia de un objeto
     * @return bool
     */
    protected function supports($attribute, $subject): bool {

        // Comprobar que el atributo (accion) se encuentra entre los dos parametros definidos en la class
        if (!in_array($attribute, [self::EDIT, self::DELETE])) {

            return false;
        }

        // Comprobar que la instancia del subject sea del tipo MicroPost
        if (!$subject instanceof MicroPost) {

            return false;
        }

        // Devolver true en caso de que las comprobaciones hayan ido bien
        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool {
        
        // Si el usuario que hay en el token tiene ROLE_ADMIN se le da los permisos
        if ($this->decisionManager->decide($token, [User::ROLE_ADMIN])) {
            
            return true;
        }

        // Conseguir el user authehticated del token
        $authenticatedUser = $token->getUser();

        // Comprobar que el usuario autentificado sea del tipo User
        if (!$authenticatedUser instanceof User) {

            return false;
        }

        /** @var MicroPost $microPost */
        $microPost = $subject;

        // Devolver si el usuario es el mismo que ha creado el post
        return ($microPost->getUser()->getId() === $authenticatedUser->getId());
        
    }

}
