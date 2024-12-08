<?php declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
final class ApiLoginController extends AbstractController
{
    #[Route('api/public/login', name: 'api.json_login', methods: 'POST')]
    public function login(): never
    {
        throw new \LogicException('Action created for route only');
    }
}
