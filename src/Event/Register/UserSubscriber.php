<?php

namespace App\Event\Register;

use App\Entity\UserPreferences;
use App\Mailer\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriber implements EventSubscriberInterface {

    /**
     * @var ParameterBagInterface
     */
    private $params;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * Constructor
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer, EntityManagerInterface $entityManager, ParameterBagInterface $params) {

        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
        $this->params = $params;
    }

    // Devolver que eventos esta class esta subscribida
    public static function getSubscribedEvents(): array {

        // Para definir un evento, se pone el nombre del evento como "key" y el valor sera la funcion que tendremos en esta class definida
        return [
            UserRegisterEvent::NAME => "onUserRegister"
        ];
    }

    /**
     * Enviar mensage al usuario que se acaba de registrar en la web, gracias a que es un evento gestionado por nosotros
     * @param UserRegisterEvent $event
     */
    public function onUserRegister(UserRegisterEvent $event) {
        
        // Coger el parametro locale definido en el services.yaml
        $locale = $this->params->get("locale");
        
        // Instancia de la class UserPreferences
        $preferences = new UserPreferences();
        
        // Definir el lenguaje
        $preferences->setLanguage($locale);
        
        // Coger el usuario que se acaba de registrar
        $user = $event->getRegisteredUser();
        
        // Establecer el parametro del idioma
        $user->setPreferences($preferences);
        
        // Actualizar los cambios, ( no se hace persist aqui, porque ya se hace en el controlador )
        $this->entityManager->flush();

        // Enviar el email de confirmacion al usuario
        $this->mailer->sendConfirmationEmail($event->getRegisteredUser());
    }

}
