<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use DateTimeImmutable;
use Faker;

class UserFixtures extends Fixture
{

    // Fonction permetant de hasher un mot de passe
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Initialisation de Faker 
        $faker = Faker\Factory::create('fr_FR');

        $userTest = new User();
        $userTest->setEmail('test@test.com');
        $userTest->setRoles(["ROLE_ADMIN"]);

        // Hash du mot de passe via l'email
        $password = $this->hasher->hashPassword($userTest, 'secret');
        $userTest->setPassword($password);
        $userTest->setLastName('TEST');
        $userTest->setFirstName('Test');
        $userTest->setIsVerified(true);
        $manager->persist($userTest);

        // Initialise l'éléments créé pour être récupérer par une autre table
        $this->addReference("user_25", $userTest);

        // Exemple fournit par l'autogéréation du fichier ->   
        // $product = new Product();
        // $manager->persist($product);

        // Insertion en BDD
        $manager->flush();
 
    }

}