<?php

namespace App\Event\Language;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class para mantener el lenguaje seleccionado por el usuario mientras este en session
 */
class LocalSubscriber implements EventSubscriberInterface {

    /**
     * @var string
     */
    private $defaultLocale;

    public function __construct(string $defaultLocale) {

        $this->defaultLocale = $defaultLocale;
    }

    public static function getSubscribedEvents(): array {

        // Para definir un evento, se pone el nombre del evento como "key" y el valor sera la funcion que tendremos en esta class definida
        // El valor numerico define la prioridad que tendra, cuanto mas alto mas tardara
        return [KernelEvents::REQUEST => [['onKernelRequest', 20]]];
    }

    public function onKernelRequest(GetResponseEvent $event) {

        $request = $event->getRequest();

        // Comprobar si el usuario tiene una session previa
        if (!$request->hasPreviousSession()) {

            return;
        }

        // Comprobar si el request tiene el atributo "_locale"
        if ($locale = $request->attributes->get("_locale")) {

            // Si lo tiene cogemos la session y le hacemos set del parametro con el valor
            $request->getSession()->set("_locale", $locale);
        } else {

            // Establecer el locale con el valor por defecto que tiene la APP
            $request->setLocale($request->getSession()->get("_locale", $this->defaultLocale));
        }
    }

}
