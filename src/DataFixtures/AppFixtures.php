<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Création d’un utilisateur de test
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setRoles(['ROLE_USER']);
        $hashedPassword = $this->hasher->hashPassword($user, 'password');
        $user->setPassword($hashedPassword);

        $manager->persist($user);

        // Création de quelques tâches
        for ($i = 1; $i <= 3; $i++) {
            $task = new Task();
            $task->setTitle("Tâche n°$i");
            $task->setDescription("Description de la tâche $i");
            $task->setDueDate(new \DateTime("+$i days"));
            $task->setUser($user);
            $manager->persist($task);
        }

        $manager->flush();
    }
}
