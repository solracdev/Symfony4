<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserPreferencesRepository")
 */
class UserPreferences {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=8)
     */
    private $language;

    // Getters

    public function getId(): ?int {
        return $this->id;
    }

    public function getLanguage() {
        return $this->language;
    }

    // SETTERS
    public function setLanguage($language) {
        $this->language = $language;
    }

}
