<?php

namespace App\Event\Register;

use App\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class UserRegisterEvent extends Event {

    // Constante con el nombre del evento que tiene esta class
    const NAME = 'user.register';

    /**
     * @var User
     */
    private $registeredUser;

    /**
     * Constructor
     * @param User $registeredUser Instancia del Usuario que se ha registrado
     */
    public function __construct(User $registeredUser) {

        $this->registeredUser = $registeredUser;
    }

    // GETTERS
    public function getRegisteredUser(): User {
        return $this->registeredUser;
    }

}
