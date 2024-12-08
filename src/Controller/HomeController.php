<?php declare(strict_types=1);

namespace App\Controller;

use App\Enum\AppRouteNameEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/', methods: 'GET')]
final class HomeController extends AbstractController
{
    public function __invoke(): RedirectResponse
    {
        //die('werdf');
        return $this->redirectToRoute(AppRouteNameEnum::ACCOUNT_LOGIN);
    }
}
