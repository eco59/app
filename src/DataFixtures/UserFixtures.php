<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new Users();
        $user->setPseudo('user');
        $user->setEmail('user@demo.fr');
        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                'Azertyuiop'
            )
        );
        $this->addReference('user', $user);

        $manager->persist($user);

        $admin = new Users();
        $admin->setPseudo('admin');
        $admin->setEmail('admin@demo.fr');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword(
            $this->passwordHasher->hashPassword(
                $admin,
                'Azertyuiop'
            )
        );
        $this->addReference('admin', $admin);

        $manager->persist($admin);


        $employee = new Users();
        $employee->setPseudo('employee');
        $employee->setEmail('employee@demo.fr');
        $employee->setRoles(['ROLE_EMPLOYEE']);
        $employee->setPassword(
            $this->passwordHasher->hashPassword(
                $employee,
                'Azertyuiop'
            )
        );
        $this->addReference('employee', $employee);

        $manager->persist($employee);



        $manager->flush();
    }
}