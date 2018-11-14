<?php

/* Para usar esta class en el security sistem tiene que implementar a la interface UserInterface, tambien para poder enciptar el password ya que
 * la class UserPasswordEncoderInterface requiere que se implemente a la UserInterface.
 * La class Serializable permite a esta entidad ser serialziada en la session y ser deserializada.
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="email", message="This e-Smail is already used.")
 * @UniqueEntity(fields="username", message="This username is already used.")
 */
class User implements UserInterface, \Serializable {

    const ROLE_USER = "ROLE_USER";
    const ROLE_ADMIN = "ROLE_ADMIN";

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(min=5, max=50)
     */
    private $username;

    /**
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=8, max=4096)
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     * @Assert\Length(min=4, max=50)
     */
    private $fullName;

    /**
     * @ORM\OneToMany(targetEntity="MicroPost", mappedBy="user")
     * @var ArrayCollection 
     */
    private $posts;

    /**
     * @ORM\Column(type="simple_array")
     * @var array
     */
    private $roles;

    //Many-to-Many self-referencing

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="following")
     */
    private $followers;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="followers")
     * @ORM\JoinTable(name="following", 
     *  joinColumns = { @ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *  inverseJoinColumns = { @ORM\JoinColumn(name="following_user_id", referencedColumnName="id")}
     * )
     */
    private $following;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\MicroPost", mappedBy="likedBy")
     */
    private $postsLiked;

    /**
     * @ORM\Column(type="string", nullable=true, length=30)
     */
    private $confirmationToken;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;
    
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\UserPreferences", cascade={"persist"})
     * Se define le cascade persist, para que cada vez que se haga una nueva instancia de la class UserPreferences
     * No se tenga que hacer un presist previamente y se hara automaticamente cuando se haga setPreferences($preferences) en el usuario
     */
    private $preferences;

    // Constructor
    public function __construct() {

        // Atributos que seran Collections
        $this->posts = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->following = new ArrayCollection();
        $this->postsLiked = new ArrayCollection();

        // Establecer el Role User
        $this->roles = [self::ROLE_USER];

        // Por defecto los usuarios no estan activados
        $this->enabled = false;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getFullName() {
        return $this->fullName;
    }

    public function getPlainPassword() {
        return $this->plainPassword;
    }

    public function getConfirmationToken() {
        return $this->confirmationToken;
    }

    public function isEnabled() {
        return $this->enabled;
    }
    
    /**
     * 
     * @return UserPreferences|null
     */
    public function getPreferences() {
        return $this->preferences;
    }

    
    /**
     * 
     * @return Collection
     */
    public function getFollowers(): Collection {
        return $this->followers;
    }

    /**
     * 
     * @return Collection
     */
    public function getFollowing(): Collection {
        return $this->following;
    }

    /**
     * 
     * @return Collection
     */
    public function getPostsLiked(): Collection {
        return $this->postsLiked;
    }

    /**
     * 
     * @return Collection
     */
    public function getPosts(): Collection {
        return $this->posts;
    }

    // Setters
    public function setUsername($username) {
        $this->username = $username;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setFullName($fullName) {
        $this->fullName = $fullName;
    }

    public function setPlainPassword($plainPassword) {
        $this->plainPassword = $plainPassword;
    }

    public function setConfirmationToken($confirmationToken) {
        $this->confirmationToken = $confirmationToken;
    }

    public function setEnabled($enabled) {
        $this->enabled = $enabled;
    }
    
    public function setPreferences($preferences) {
        $this->preferences = $preferences;
    }

    
    /**
     * 
     * @param array $roles
     * @return void
     */
    public function setRoles(array $roles): void {
        $this->roles = $roles;
    }

    // Implemented Functions

    public function eraseCredentials() {
        
    }

    public function getRoles() {

        return $this->roles;
    }

    public function getSalt() {

        return null;
    }

    public function getPassword() {

        return $this->password;
    }

    public function getUsername() {

        return $this->username;
    }

    /**
     * Serializar las propiedades para poder hacer el login ya que el security component las necesita
     * @return string
     */
    public function serialize(): string {

        return serialize([
            $this->id,
            $this->username,
            $this->password,
            $this->enabled
        ]);
    }

    /**
     * Deserialziar las propiedades que previamente hemos serializado
     * @param type $serialized
     * @return void
     */
    public function unserialize($serialized): void {

        list(
                $this->id,
                $this->username,
                $this->password,
                $this->enabled) = unserialize($serialized);
    }

    /**
     * Añadir un usuario a la collection en caso que no exista ya.
     * @param \App\Entity\User $user
     * @return type
     */
    public function follow(User $user) {

        if ($this->following->contains($user)) {

            return;
        }

        $this->getFollowing()->add($user);
    }
}
