<?php

namespace App\DataFixtures;

use App\Entity\Stock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use DateTimeImmutable;
use Faker;

class StockFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {

        // Initialisation de Faker 
        $faker = Faker\Factory::create('fr_FR');

        // Boucle for() pour insérer un certains nombre de données
        for($i = 0; $i < 100; $i++){

            $today = new DateTimeImmutable('now');
            $randomDate = $today->modify('-'.random_int(0, 1095).'days');

            // Instancie l'entité avec laquelle travailler
            $stock = new Stock();
            $stock->setName($faker->city);
            $stock->setPlace($faker->randomLetter.$faker->buildingNumber);
            $stock->setCreatedAt($randomDate);

            $stock->setMagazine($this->getReference("magazine_".rand(0, 29)));

            // Met de côté les données en attente d'insertion 
            $manager->persist($stock);

        }

        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }

    public function getDependencies(){
        return [
            MagazineFixtures::class
        ];
    }
}