<?php

namespace App\DataFixtures;

use App\Entity\SiteRedirect;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $redirect = new SiteRedirect();
        $redirect->setAlias('admin_auth');
        $redirect->setRedirectUrl('/admin/login');
        $manager->persist($redirect);

        $manager->flush();
    }
}
