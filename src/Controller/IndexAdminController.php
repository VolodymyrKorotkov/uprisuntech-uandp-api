<?php declare(strict_types=1);

namespace App\Controller;

use App\Controller\Admin\ApplicationTaskCrudController;
use App\Controller\Admin\ApplicationTypeCrudController;
use App\Controller\Admin\GuideBookCRUDController;
use App\Controller\Admin\NativeStrategyCrudController;
use App\Controller\Admin\OauthSiteRedirectCRUDController;
use App\Controller\Admin\OrganizationCrudController;
use App\Controller\Admin\OrganizationJoinFlowController;
use App\Controller\Admin\OrganizationJoinInviteController;
use App\Controller\Admin\OrganizationJoinTaskController;
use App\Controller\Admin\OrganizationUserRoleController;
use App\Controller\Admin\SyncUserFromKeycloakCrudController;
use App\Controller\Admin\UserCrudController;
use App\Entity\ApplicationProcess;
use App\Entity\ApplicationTask;
use App\Entity\ApplicationType;
use App\Entity\CamundaStrategy;
use App\Entity\CamundaTaskFilter;
use App\Entity\EmailTemplate;
use App\Entity\FormIo;
use App\Entity\FormProcessSubmission;
use App\Entity\FormProcessSubmissionVariable;
use App\Entity\FormSubmissionEditLocker;
use App\Entity\FormTaskSubmission;
use App\Entity\GuideBook;
use App\Entity\InstallerEmail;
use App\Entity\NativeStrategy;
use App\Entity\Organization;
use App\Entity\OrganizationJoinFlow;
use App\Entity\OrganizationJoinInvite;
use App\Entity\OrganizationJoinTask;
use App\Entity\OrganizationUserRole;
use App\Entity\SiteRedirect;
use App\Entity\SyncUserFromKeycloak;
use App\Entity\User;
use App\Enum\ApplicationStrategyEnum;
use App\Enum\AppRouteNameEnum;
use App\Enum\EntityCrudRoleEnum;
use App\Enum\UserRoleEnum;
use App\Repository\ApplicationTypeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityNotFoundException;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\CrudMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\DashboardMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\SectionMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\SubMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\UrlMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsController]
final class IndexAdminController extends AbstractDashboardController
{
    public function __construct(
        #[Autowire(env: 'KEYCLOAK_URL')] private readonly string $keycloakUrl,
        //todo: use http client
        private readonly UserRepository                          $userRepository,
        private readonly ApplicationTypeRepository               $applicationTypeRepository,
    )
    {
    }

    #[Route('/admin', name: AppRouteNameEnum::ACCOUNT_INDEX)]
    public function index(): Response
    {
        return $this->render('@EasyAdmin/page/content.html.twig');
    }

    /**
     * @throws EntityNotFoundException
     */
    public function configureUserMenu(UserInterface $user): UserMenu
    {
        $appUser = $this->userRepository->getByUserIdentity($user->getUserIdentifier());

        return parent::configureUserMenu($user)
            ->setName($appUser->getEmail() ?? ($appUser->getName() . ' ' . $appUser->getLastname()));
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('uandp - Main Admin')
            ->renderContentMaximized()
            //  ->renderSidebarMinimized()
            ->disableDarkMode()
            ->generateRelativeUrls();
    }

    public function configureAssets(): Assets
    {
        return parent::configureAssets()
            ->addWebpackEncoreEntry('app')
            //            ->addCssFile(
//                Asset::new('https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css')->ignoreOnIndex()
//            )
//            ->addCssFile(
//                Asset::new('https://cdn.form.io/formiojs/formio.full.min.css')->ignoreOnIndex()
//            )
//            ->addJsFile(
//                Asset::new('https://cdn.form.io/formiojs/formio.full.min.js')->ignoreOnIndex()
//            )

            ;
    }

    public function configureCrud(): Crud
    {
        return parent::configureCrud()
            ->overrideTemplate('layout', 'layout.html.twig');
    }


    public function configureMenuItems(): iterable
    {
        yield $this->getDashboardMenuItem();

        yield $this->getUserSection();
        yield $this->getUsersMenuItem();
        yield $this->getSyncKeycloakMenuItem();
        yield $this->getKeycloakMenuItem();

        yield $this->getApplicationSection();
        yield $this->getProcessMenuItem();
        yield $this->getApplicationTaskMenuItem();
        yield $this->getCamundaTaskMenuItem();

        //  yield $this->getCamundaTaskFilterMenuItem();

        yield $this->getApplicationSettingsSection();
        yield $this->getFormIoMetadata();
        yield $this->applicationType();
        yield $this->getNativeStrategyMenuItem();
        yield $this->getCamundaStrategyMenuItem();
        yield $this->getInstallerEmailMenuItem();

        yield $this->getProcessSubmissionLink();
        yield $this->getFormProcessSubmission();
        yield $this->getFormProcessSubmissionVariable();
        yield $this->getFormTaskSubmission();
        yield $this->getFormSubmissionEditLocker();

        yield $this->getOrganizationSection();
        yield $this->getOrganizationMenuItem();
        yield $this->getOrganizationUserRoleMenuItem();
        yield $this->getOrganizationJoinMenuItem();
        yield $this->getOrganizationJoinTaskMenuItem();
        yield $this->getOrganizationJoinInviteMenuItem();

        yield $this->getSettingSection();
        yield $this->getGuideBookItem();
        yield $this->getSiteRedirectMenuItem();
        yield $this->getEmailTemplateMenuItem();
    }

    /**
     * @return CrudMenuItem
     */
    public function getGuideBookItem(): CrudMenuItem
    {
        return MenuItem::linkToCrud('Desc Book', 'fa fa-tags', GuideBook::class)
            ->setPermission(GuideBookCRUDController::getViewRole()->value);
    }

    /**
     * @return SectionMenuItem
     */
    public function getSettingSection(): SectionMenuItem
    {
        return MenuItem::section('Settings')
            ->setPermission(UserRoleEnum::ROLE_SUPER_ADMIN_CASE->value);
    }

    /**
     * @return CrudMenuItem
     */
    public function applicationType(): CrudMenuItem
    {
        return MenuItem::linkToCrud('Application Type', 'fa fa-tags', ApplicationType::class)
            ->setPermission(ApplicationTypeCrudController::getViewRole()->value);
    }

    private function getNativeStrategyMenuItem(): CrudMenuItem
    {
        return MenuItem::linkToCrud('Native strategy', 'fa fa-tags', NativeStrategy::class)
            ->setPermission(NativeStrategyCrudController::getViewRole()->value);
    }

    private function getKeycloakMenuItem(): UrlMenuItem
    {
        return MenuItem::linkToUrl('Keycloak', 'fa fa-tags', $this->keycloakUrl)
            ->setLinkTarget('blank')
            ->setPermission(UserRoleEnum::ROLE_SUPER_ADMIN_CASE->value);
    }

    /**
     * @return DashboardMenuItem
     */
    public function getDashboardMenuItem(): DashboardMenuItem
    {
        return MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
    }

    /**
     * @return SectionMenuItem
     */
    public function getUserSection(): SectionMenuItem
    {
        return MenuItem::section('User')
            ->setPermission(UserCrudController::getViewRole()->value);
    }

    public function getUsersMenuItem(): CrudMenuItem
    {
        return MenuItem::linkToCrud('Users', 'fa fa-tags', User::class)
            ->setPermission(UserCrudController::getViewRole()->value);
    }

    /**
     * @return CrudMenuItem
     */
    public function getSyncKeycloakMenuItem(): CrudMenuItem
    {
        return MenuItem::linkToCrud('Sync user from keycloak', 'fa fa-tags', SyncUserFromKeycloak::class)
            ->setPermission(SyncUserFromKeycloakCrudController::getViewRole()->value);
    }

    /**
     * @return CrudMenuItem
     */
    public function getSiteRedirectMenuItem(): CrudMenuItem
    {
        return MenuItem::linkToCrud('Site redirect', 'fa fa-tags', SiteRedirect::class)
            ->setPermission(OauthSiteRedirectCRUDController::getViewRole()->value);
    }

    /**
     * @return CrudMenuItem
     */
    public function getApplicationTaskMenuItem(): CrudMenuItem
    {
        return MenuItem::linkToCrud('Native tasks', 'fa fa-tags', ApplicationTask::class)
            ->setPermission(ApplicationTaskCrudController::getViewRole()->value);
    }

    /**
     * @return CrudMenuItem
     */
    public function getOrganizationMenuItem(): CrudMenuItem
    {
        return MenuItem::linkToCrud('Organization', 'fa fa-tags', Organization::class)
            ->setPermission(OrganizationCrudController::getViewRole()->value);
    }

    /**
     * @return CrudMenuItem
     */
    public function getOrganizationUserRoleMenuItem(): CrudMenuItem
    {
        return MenuItem::linkToCrud('Organization Role', 'fa fa-tags', OrganizationUserRole::class)
            ->setPermission(OrganizationUserRoleController::getViewRole()->value);
    }

    /**
     * @return CrudMenuItem
     */
    public function getOrganizationJoinMenuItem(): CrudMenuItem
    {
        return MenuItem::linkToCrud('Join to organization', 'fa fa-tags', OrganizationJoinFlow::class)
            ->setPermission(OrganizationJoinFlowController::getViewRole()->value);
    }

    /**
     * @return CrudMenuItem
     */
    public function getOrganizationJoinTaskMenuItem(): CrudMenuItem
    {
        return MenuItem::linkToCrud('Join task', 'fa fa-tags', OrganizationJoinTask::class)
            ->setPermission(OrganizationJoinTaskController::getViewRole()->value);
    }

    /**
     * @return CrudMenuItem
     */
    public function getOrganizationJoinInviteMenuItem(): CrudMenuItem
    {
        return MenuItem::linkToCrud('Join invite', 'fa fa-tags', OrganizationJoinInvite::class)
            ->setPermission(OrganizationJoinInviteController::getViewRole()->value);
    }

    private function getFormIoMetadata(): CrudMenuItem
    {
        return MenuItem::linkToCrud('Form', 'fa fa-tags', FormIo::class)
            ->setPermission(
                EntityCrudRoleEnum::newFromEntityClass(FormIo::class)->getDefaultRole()->value
            );
    }

    private function getApplicationSection(): SectionMenuItem
    {
        return MenuItem::section('Application')
            ->setPermission(UserRoleEnum::ROLE_SUPER_ADMIN_CASE->value);
    }

    private function getApplicationSettingsSection(): SectionMenuItem
    {
        return MenuItem::section('Application settings')
            ->setPermission(UserRoleEnum::ROLE_SUPER_ADMIN_CASE->value);
    }

    private function getOrganizationSection(): SectionMenuItem
    {
        return MenuItem::section('Organization')
            ->setPermission(UserRoleEnum::ROLE_SUPER_ADMIN_CASE->value);
    }

    private function getProcessSubmissionLink(): SectionMenuItem
    {
        return MenuItem::section('Process submission link')
            ->setPermission(UserRoleEnum::ROLE_SUPER_ADMIN_CASE->value);
    }

    /**
     * @return CrudMenuItem
     */
    public function getCamundaStrategyMenuItem(): CrudMenuItem
    {
        return MenuItem::linkToCrud('Camunda settings', 'fa fa-tags', CamundaStrategy::class)
            ->setPermission(
                EntityCrudRoleEnum::newFromEntityClass(CamundaStrategy::class)->getDefaultRole()->value
            );
    }

    private function getCamundaTaskMenuItem(): SubMenuItem
    {
        $items = [];
        foreach ($this->applicationTypeRepository->findByStrategy(ApplicationStrategyEnum::CAMUNDA) as $appType){
            $items[] = MenuItem::linkToRoute(
                $appType->getTitle(),
                'fa fa-tags',
                routeName: AppRouteNameEnum::ADMIN_CAMUNDA_TASKS_ROUTE->value,
                routeParameters: ['typeId' => $appType->getId()]
            );
        }

        return MenuItem::subMenu('Camunda tasks', 'fa fa-chart-bar')
            ->setSubItems($items)
            ->setPermission(UserRoleEnum::ROLE_CAMUNDA_TASK->value);
    }

    private function getCamundaTaskFilterMenuItem(): CrudMenuItem
    {
        return MenuItem::linkToCrud('Task Filter', 'fa fa-chart-ba', CamundaTaskFilter::class)
            ->setPermission(
                EntityCrudRoleEnum::newFromEntityClass(CamundaTaskFilter::class)->getDefaultRole()->value
            );
    }

    private function getFormProcessSubmission(): CrudMenuItem
    {
        return MenuItem::linkToCrud('Process submissions', 'fa fa fa-tags', FormProcessSubmission::class)
            ->setPermission(
                EntityCrudRoleEnum::newFromEntityClass(FormProcessSubmission::class)->getDefaultRole()->value
            );
    }

    private function getProcessMenuItem(): CrudMenuItem
    {
        return MenuItem::linkToCrud('Processes', 'fa fa fa-tags', ApplicationProcess::class)
            ->setPermission(
                EntityCrudRoleEnum::newFromEntityClass(ApplicationProcess::class)->getDefaultRole()->value
            );
    }

    private function getFormProcessSubmissionVariable(): CrudMenuItem
    {
        return MenuItem::linkToCrud('Processes submission variable', 'fa fa fa-tags', FormProcessSubmissionVariable::class)
            ->setPermission(
                EntityCrudRoleEnum::newFromEntityClass(FormProcessSubmissionVariable::class)->getDefaultRole()->value
            );
    }

    private function getFormTaskSubmission(): CrudMenuItem
    {
        return MenuItem::linkToCrud('Task submission', 'fa fa fa-tags', FormTaskSubmission::class)
            ->setPermission(
                EntityCrudRoleEnum::newFromEntityClass(FormTaskSubmission::class)->getDefaultRole()->value
            );
    }

    private function getInstallerEmailMenuItem(): CrudMenuItem
    {
        return MenuItem::linkToCrud('Installers', 'fa fa fa-tags', InstallerEmail::class)
            ->setPermission(
                EntityCrudRoleEnum::newFromEntityClass(InstallerEmail::class)->getDefaultRole()->value
            );
    }

    private function getEmailTemplateMenuItem(): CrudMenuItem
    {
        return MenuItem::linkToCrud('Email templates', 'fa fa fa-tags', EmailTemplate::class)
            ->setPermission(
                EntityCrudRoleEnum::newFromEntityClass(EmailTemplate::class)->getDefaultRole()->value
            );
    }

    private function getFormSubmissionEditLocker(): CrudMenuItem
    {
        return MenuItem::linkToCrud('Lock submission edit', 'fa fa fa-tags', FormSubmissionEditLocker::class)
            ->setPermission(
                EntityCrudRoleEnum::newFromEntityClass(FormSubmissionEditLocker::class)->getDefaultRole()->value
            );

    }
}
