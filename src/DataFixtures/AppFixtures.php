<?php

namespace App\DataFixtures;

use App\Entity\Building;
use App\Entity\Organization;
use App\Entity\Room;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create("fr_FR");
        
        for($i = 0; $i < 15; $i++){
            $organization = new Organization();
            $organization
                ->setName($faker->word());
            
            $manager->persist($organization);
            
            for($j = 0; $j < rand(2, 15); $j++){
                $building = new Building();
                $building
                    ->setName($faker->word())
                    ->setZipcode($faker->randomNumber(5, true))
                    ->setOrganization($organization);
                    
                $manager->persist($building);
                
                for($k = 0; $k < rand(1, 15); $k++){
                    $room = new Room();
                    $room
                        ->setName($faker->name)
                        ->setPeoples($faker->numberBetween(10, 25))
                        ->setBuilding($building);
                    
                    $manager->persist($room);
                }
            }
        }
        
        $manager->flush();
    }
}
