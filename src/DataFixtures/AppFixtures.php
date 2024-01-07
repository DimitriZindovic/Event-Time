<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Place;
use App\Entity\Event;
use Faker\Factory;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr_FR');

        $user = new User();
        $user->setEmail('dimitri.zindovic@gmail.com');
        $user->setPassword($this->hasher->hashPassword($user, 'password'));
        $user->setRoles(['ROLE_ADMIN']);
        $user->setIsVerified(true);
        $manager->persist($user);

        for ($i = 0; $i < 10; $i++) {
            $place = new Place();
            $place->setCity($faker->country);
            $manager->persist($place);
            $this->addReference("place-" . $i, $place);
        }


        for ($i = 0; $i < 10; $i++) {
            $category = new Category();
            $category->setName($faker->word);
            $manager->persist($category);
            $this->addReference("category-" . $i, $category);
        }

        $names = ["Concert", "Cinéma", "Plage", "Théâtre", "Musée", "Exposition", "Conférence", "Festival", "Soirée", "Bar"];

        foreach ($names as $name) {
            $event = new Event();
            $event->setName($name);
            $event->setPrice($faker->randomFloat(2, 10, 100));
            $event->setStartAt($startAt = $faker->dateTimeBetween('-15 days', '+15 days'));
            $event->setEndAt((clone $startAt)->modify('+' . rand(1, 72) . ' hours'));

            $event->setPlace($this->getReference("place-" . rand(0, 9)));

            $keys = (array) array_rand(range(0, 9), rand(1, 3));
            foreach ($keys as $key) {
                $event->addCategory($this->getReference("category-" . $key));
            }

            $manager->persist($event);
        }

        $manager->flush();
    }
}
