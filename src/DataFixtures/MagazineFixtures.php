<?php

namespace App\DataFixtures;

use App\Entity\Magazine;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use DateTimeImmutable;
use Faker;

class MagazineFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {

        // Initialisation de Faker 
        $faker = Faker\Factory::create('fr_FR');

        // Boucle for() pour insérer un certains nombre de données
        for($i = 0; $i < 30; $i++){

            $today = new DateTimeImmutable('now');
            $randomDate = $today->modify('-'.random_int(0, 1095).'days');

            // Instancie l'entité avec laquelle travailler
            $magazine = new Magazine();
            $magazine->setName($faker->company);
            $magazine->setDescription($faker->realText(120, 2));
            $magazine->setPrice(random_int(5, 16));
            $magazine->setCreatedAt($randomDate);

            $magazine->setCategory($this->getReference("category_".rand(0, 9)));

            $this->addReference("magazine_$i", $magazine);

            // Met de côté les données en attente d'insertion 
            $manager->persist($magazine);

        }

        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }

    public function getDependencies(){
        return [
            CategoryFixtures::class
        ];
    }

}
