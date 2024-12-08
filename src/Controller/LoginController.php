<?php declare(strict_types=1);

namespace App\Controller;

use App\Enum\AppRouteNameEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[AsController]
final class LoginController extends AbstractController
{
    #[Route('/admin/login', name: AppRouteNameEnum::ACCOUNT_LOGIN)]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername,

            'page_title' => 'ACME login',

            // the string used to generate the CSRF token. If you don't define
            // this parameter, the login form won't include a CSRF token
            'csrf_token_intention' => 'authenticate',
            'target_path' => $this->generateUrl(AppRouteNameEnum::ACCOUNT_INDEX),
            'username_label' => 'Your username',
            'password_label' => 'Your password',
            'sign_in_label' => 'Log in',
        ]);
    }

    #[Route('/admin/logout', name: AppRouteNameEnum::ACCOUNT_LOGOUT)]
    public function logout(Security $security): RedirectResponse
    {

    }
}
