<?php

namespace App\Event\Language;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class UserLocalSubscriber implements EventSubscriberInterface {

    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(SessionInterface $session) {

        $this->session = $session;
    }

    public static function getSubscribedEvents(): array {

        return [
            SecurityEvents::INTERACTIVE_LOGIN => [
                [
                    'onInteractiveLogin',
                    15
                ]
            ]
        ];
    }

    /**
     * Cuando un usuario haga login, se pondra el valor del idioma que tiene automaticamente
     * @param InteractiveLoginEvent $event
     */
    public function onInteractiveLogin(InteractiveLoginEvent $event) {
        
        // Coger el usuario del evento
        $user = $event->getAuthenticationToken()->getUser();
        
        // Establecer el valor del idioma que tiene el usuario, en la session
        $this->session->set("_locale", $user->getPreferences()->getLanguage());
        
    }

}
