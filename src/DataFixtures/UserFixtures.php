<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;


class UserFixtures extends Fixture
{

    public function __construct(UserPasswordHasherInterface $password_hasher)
    {
        $this->password_hasher = $password_hasher;
    }

    public function load(ObjectManager $manager)
    {
        foreach ($this->getUserData() as [$first_name, $last_name, $email, $password, $api_key, $roles]) {
            $user = new User();
            $user->setFirstName($first_name);
            $user->setLastName($last_name);
            $user->setEmail($email);
            $user->setPassword($this->password_hasher->hashPassword($user, $password));
            $user->setVimeoApiKey($api_key);
            $user->setRoles($roles);

            $manager->persist($user);
        }

        $manager->flush();
    }


    private function getUserData(): array
    {
        return [

            ['Abdi', 'Ahmed', 'aa@gm.com', '111111', 'hjd8dehdh', ['ROLE_ADMIN']],
            ['Mark', 'Wayne', 'mw@gm.com', '111111', null, ['ROLE_ADMIN']],
            ['John', 'Doe', 'jd@gm.com', '111111', null, ['ROLE_USER']],
            ['Anne', 'Doe', 'ad@gm.com', '111111', null, ['ROLE_USER']]

        ];
    }
}
