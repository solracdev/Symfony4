<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use App\Entity\User;
use App\Entity\UserPreferences;
use DateInterval;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture {

    private const USERS = [
        [
            'username' => 'carlos',
            'email' => 'carlos@carlos.com',
            'password' => '123456',
            'fullName' => 'Carlos Garcia',
            'roles' => [User::ROLE_ADMIN]
        ],
        [
            'username' => 'john_doe',
            'email' => 'john_doe@doe.com',
            'password' => 'john123',
            'fullName' => 'John Doe',
            'roles' => [User::ROLE_USER]
        ],
        [
            'username' => 'rob_smith',
            'email' => 'rob_smith@smith.com',
            'password' => 'rob12345',
            'fullName' => 'Rob Smith',
            'roles' => [User::ROLE_USER]
        ]
    ];
    private const POST_TEXT = [
        'Hello, how are you?',
        'It\'s nice sunny weather today',
        'I need to buy some ice cream!',
        'I wanna buy a new car',
        'There\'s a problem with my phone',
        'I need to go to the doctor',
        'What are you up to today?',
        'Did you watch the game yesterday?',
        'How was your day?'
    ];
    private const LANGUAGE = [
        "en",
        "fr",
        "es"
    ];

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEnconder;

    public function __construct(UserPasswordEncoderInterface $passwordEnconder) {

        $this->passwordEnconder = $passwordEnconder;
    }

    public function load(ObjectManager $manager) {

        $this->loadUsers($manager);
        $this->loadMicroPosts($manager);
    }

    /**
     * metodo para cargar fake data
     * @param ObjectManager $manager
     */
    public function loadMicroPosts(ObjectManager $manager) {

        for ($i = 0; $i < 30; $i++) {

            $microPost = new MicroPost();

            // En php para imitar al array.length() - 1 de java se hace asi, count(array) - 1
            $microPost->setText(self::POST_TEXT[rand(0, count(self::POST_TEXT) - 1)]);

            $date = new \DateTime();
            // ir restando dias de manera aleatoria se puede hacer de dos maneras
            $date->sub(new DateInterval("P" . rand(0, 20) . "D"));
            //$date->modify('-' . rand(0, 20) . "day");
            $microPost->setTime($date);

            // Añadir al post el user con la referencia creada en el metodo loadUsers
            $microPost->setUser($this->getReference(self::USERS[rand(0, count(self::USERS) - 1)]['username']));

            $manager->persist($microPost);
        }

        $manager->flush();
    }

    public function loadUsers(ObjectManager $manager) {

        foreach (self::USERS as $users) {

            $user = new User();
            $user->setUsername($users['username']);
            $user->setEmail($users['email']);
            $user->setFullName($users['fullName']);
            $user->setRoles($users["roles"]);
            $user->setEnabled(true);
            $user->setPassword($this->passwordEnconder->encodePassword($user, $users['password']));

            $preferences = new UserPreferences();
            $preferences->setLanguage(self::LANGUAGE[rand(0, 2)]);

            $user->setPreferences($preferences);

            // Añadir las referencias para las relaciones ManyToOne y OneToMany
            $this->addReference($users['username'], $user);

            $manager->persist($user);
        }

        $manager->flush();
    }

}
