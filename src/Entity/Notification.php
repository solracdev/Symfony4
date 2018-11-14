<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

//TABLE INHERITANCE

/**
 * @ORM\Entity(repositoryClass="App\Repository\NotificationRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"like" = "LikeNotification"})
 */
abstract class Notification {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $user;

    /**
     * @ORM\Column(type="boolean")
     */
    private $seen;

    // CONSTRUCTOR
    public function __construct() {

        // Establecer el campo seen por defecto en false
        $this->seen = false;
    }

    // GETTERS
    public function getId(): ?int {
        return $this->id;
    }

    public function getUser() {
        return $this->user;
    }

    public function getSeen() {
        return $this->seen;
    }

    // SETTERS
    public function setUser($user) {
        $this->user = $user;
    }

    public function setSeen($seen) {
        $this->seen = $seen;
    }

}
