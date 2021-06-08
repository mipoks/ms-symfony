<?php


namespace App\Controller;

use App\Entity\Person;
use App\Form\PersonForm;
use App\Repository\PersonRepository;
use App\Services\Constants;
use App\Services\SignUpService;
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

/**
 * @Route("/register")
 */
class RegisterController extends AbstractController
{
    /**
     * @Route(methods="GET", name="register")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request, SignUpService $signUpService, Security $security, AuthenticationUtils $helper): Response
    {
        return $this->render(
            'register.html.twig', ['title' => 'Регистрация']
        );
    }

    /**
     * @Route(methods="POST", name="register_post")
     * @param Request $request
     * @return Response
     */
    public function register(Request $request, SignUpService $signUpService, Security $security, AuthenticationUtils $helper): Response
    {
        $personForm = new PersonForm();
        $personForm->setName($request->request->get('name'));
        $personForm->setPassword($request->request->get('password'));
        $personForm->setPwd2($request->request->get('pwd2'));
        $personForm->setAgree($request->request->get('agree'));
        $personForm->setEmail($request->request->get('email'));
        echo("<script>console.log('from profileController:" . json_encode($personForm) . "');</script>");

        $alertInfo = new Alert();
        $result = $personForm->isValid();
        echo("<script>console.log('from profileController:" . $result. "');</script>");

        switch ($result) {
            case Constants::AGREEMENT_FALSE:
                $alertInfo->setBody("Вы должны согласиться с правилами");
                break;
            case Constants::EMAIL_INCORRECT:
                $alertInfo->setBody("Введите корректный Email");
                break;
            case Constants::NAME_SIZE_ERROR:
                $alertInfo->setBody("Имя должно содержать от 2 до 30 символов");
                break;
            case Constants::PWD_SIZE_LONG:
            case Constants::PWD_SIZE_SHORT:
                $alertInfo->setBody("Пароль должен содержать от 6 до 30 символов");
                break;
            case Constants::PWDS_NOT_EQUALS:
                $alertInfo->setBody("Пароли различаются");
                break;
        }
        if ($alertInfo->getBody() == null) {
            $registrationResult = $signUpService->signUp($personForm);
            if ($registrationResult == Constants::SUCCESS) {
                $signUpService->signUp($personForm);
                return $this->redirectToRoute('login');
            } else {
                $alertInfo->setBody("Пользователь уже зарегистрирован");
            }
        }
        $alertInfo->setHead(Alert::$HEAD_DANGER);
        $alertInfo->setColor(Alert::$COLOR_DANGER);

        return $this->render(
            'register.html.twig', ['title' => 'Регистрация',
            'personForm' => $personForm,
                'info' => $alertInfo]
        );

    }
}
