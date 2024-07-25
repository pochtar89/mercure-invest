<?php

// src/DataFixtures/AppFixtures.php
namespace App\DataFixtures;

use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        foreach (range(1, 10) as $num) {
            UserFactory::createOne([
                'email' => "user$num@mer.cure",
                'password' => password_hash("user$num@mer.cure", PASSWORD_BCRYPT),
                'avatar' => "avatars/$num.png",
            ]);
        }
    }
}
