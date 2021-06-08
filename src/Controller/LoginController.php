<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Util\Alert;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;


/**
 * @Route("/login")
 */
class LoginController extends AbstractController
{
    private $alert;

    public function __construct()
    {
        $this->alert = new Alert();
    }


    use TargetPathTrait;
    /**
     * @Route(methods="GET|POST", name="login")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request, Security $security, AuthenticationUtils $helper): Response
    {
        if ($security->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('me');
        }

        $params = ['username' => $helper->getLastUsername(), 'title' => 'Войти'];
        $this->saveTargetPath($request->getSession(), 'main', $this->generateUrl('search'));
        if ($helper->getLastAuthenticationError() != null) {
            $this->alert->setBody("Неверный логин или пароль");
            $this->alert->setColor(Alert::$COLOR_DANGER);
            $this->alert->setHead(Alert::$HEAD_DANGER);
            $params['info'] = $this->alert;
        }
        return $this->render('login.html.twig', $params);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(): void
    {
        throw new \Exception('This should never be reached!');
    }

}
