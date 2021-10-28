<?php

namespace App\DataFixtures;

use App\Entity\Genre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GenreFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        foreach ($this->getMainCategoriesData() as [$name]) {

            $genre = new Genre();
            $genre->setName($name);
            $manager->persist($genre);
        }

        $manager->flush();
    }

    private function getMainCategoriesData()
    {
        return [
            ['Action'],
            ['Comedy'],
            ['Drama'],
            ['crime'],
            ['Nature'],
            ['Science']

        ];
    }
}
