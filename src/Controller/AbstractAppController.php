<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class AbstractAppController extends AbstractController
{
    protected const BASE_AUTH_URL = 'auth';
}