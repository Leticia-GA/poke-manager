<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $this->adminFixtures($manager);
        $this->userFixtures($manager);
    }

    public function adminFixtures(ObjectManager $manager): void
    {
        $admin = new User(
            'Leticia',
            'admin@gmail.com',
            User::ROLE_ADMIN
        );

        $hashedPassword = $this->passwordHasher->hashPassword(
            $admin,
            'admin'
        );

        $admin->setPassword($hashedPassword);

        $manager->persist($admin);
        $manager->flush();
    }

    public function userFixtures(ObjectManager $manager): void
    {
        $user = new User(
            'Ash',
            'user@gmail.com',
            User::ROLE_USER
        );

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            'user'
        );

        $user->setPassword($hashedPassword);

        $manager->persist($user);
        $manager->flush();
    }
}
