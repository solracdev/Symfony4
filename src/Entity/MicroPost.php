<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MicroPostRepository")
 * @ORM\Table()
 * @ORM\HasLifecycleCallbacks()
 */
class MicroPost {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=280)
     * @Assert\NotBlank()
     * // dentro de Length, se puede editar el mensaje de error con la variable: minMessage="texto de error"
     * @Assert\Length(min="10", max="100")
     */
    private $text;

    /**
     * @ORM\Column(type="datetime")
     */
    private $time;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="postsLiked")
     * @ORM\JoinTable(name="post_likes",
     *      joinColumns = {@ORM\JoinColumn(name="post_id", referencedColumnName="id")},
     *      inverseJoinColumns = {@ORM\joinColumn(name="user_id", referencedColumnName="id")}
     * )
     */
    private $likedBy;

    // CONSTRUCTOR
    public function __construct() {

        $this->likedBy = new ArrayCollection();
    }

    // Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getText() {
        return $this->text;
    }

    public function getTime() {
        return $this->time;
    }

    /**
     * 
     * @return Collection
     */
    public function getLikedBy(): Collection {
        return $this->likedBy;
    }

    /**
     * 
     * @return User
     */
    public function getUser() {
        return $this->user;
    }

    // Setters
    public function setText($text) {
        $this->text = $text;
    }

    public function setTime($time) {
        $this->time = $time;
    }

    public function setUser($user) {
        $this->user = $user;
    }

    /**
     * @ORM\PrePersist()
     */
    public function setTimeOnPersist(): void {

        // utilizando la anotacion ORM\PrePersist(), nos permite callback y llamar a este metodo antes de insertart la entidad en la BBDD

        $this->time = new \DateTime();
    }

    /**
     * Añadir un like del usuario si no lo contiene
     * @param \App\Entity\User $user
     * @return type
     */
    public function likePost(User $user) {

        if ($this->likedBy->contains($user)) {

            return;
        }

        $this->likedBy->add($user);
    }

}
