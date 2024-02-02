<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class UserFixtures extends Fixture
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher){
        $this->hasher= $hasher;

    }
    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr-Fr');

        for ($i =0; $i< 101;$i++){
            $user = new User();
            $user->setNom($faker->lastName);
            $user->setPrenom($faker->firstName);
            $user->setUsername($faker->userName);
            $user->setPassword($faker->password());
            $manager->persist($user);
        }

        $admin = new User();
        $admin->setPassword($this->hasher->hashPassword($admin,'admin'));
        $admin->setUsername("admin");
        $admin->setPrenom("Aurélien");
        $admin->setNom("Delorme");
        $admin->setRoles(["ROLE_ADMIN"]);
        $manager->persist($admin);

        $usertest = new User();
        $usertest->setPassword($this->hasher->hashPassword($usertest,'user'));
        $usertest->setUsername("user");
        $usertest->setPrenom("Maxime");
        $usertest->setNom("Martinez");
        $manager->persist($usertest);


        $manager->flush();
    }
}
