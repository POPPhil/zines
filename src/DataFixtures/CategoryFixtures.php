<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Initialisation de Faker 
        $faker = Faker\Factory::create();

        // Boucle for() pour insérer un certains nombre de données
        for($i = 0; $i < 10; $i++){
 
            // Instancie l'entité avec laquelle travailler
            $category = new Category();
            $category->setName($faker->jobTitle);
            $category->setColor($faker->hexcolor);

            $this->addReference("category_$i", $category);
             
            // Met de côté les données en attente d'insertion 
            $manager->persist($category);
 
        }
 
        // $product = new Product();
        // $manager->persist($product);
 
        $manager->flush();
    }

}
