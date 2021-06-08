<?php


namespace App\Controller;

use App\Form\PersonForm;
use App\Services\PersonGiver;
use App\Services\ProfileService;
use App\Services\SignUpService;
use App\Util\Alert;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/profile")
 */
class ProfileController extends AbstractController
{
    private $headSuccess = "Настройки сохранены!";
    private $bodySuccess = "Изменения вступили в силу";
    private $bodyDanger = "Изменения не сохранены. Попробуйте позднее";

    private $info;

    public function __construct(ProfileService $profileService, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->profileService = $profileService;
        $this->passwordEncoder = $passwordEncoder;
        $this->info = new Alert();
    }


    private $profileService;
    private $passwordEncoder;

    /**
     * @Route(methods="GET", name="profile")
     * @param Request $request
     * @param PersonGiver $personGiver
     * @return Response
     */
    public function index(Request $request, PersonGiver $personGiver): Response
    {
        $person = $personGiver->get();
        return $this->render('profile.html.twig', [
            'user' => $person,
            'title' => "Настройки",
        ]);
    }


    /**
     * @Route(methods="POST", name="profile_post")
     * @param Request $request
     * @param PersonGiver $personGiver
     * @return Response
     */
    public function editProfile(Request $request, PersonGiver $personGiver,
                                SignUpService $signUpService): Response
    {
        $person = $personGiver->get();

        $del = false;
        $error = false;

        $personForm = new PersonForm();

        $passworddelete = $request->request->get('passworddelete');

        if ($passworddelete != null)
            $del = true;
        if ($passworddelete != null && $passworddelete != "") {
            try {
                if ($this->passwordEncoder->isPasswordValid($person, $passworddelete)) {
                    echo ("<script>console.log('from profileController: удаляем!');</script>");
                    $signUpService->expel($person);
                    $this->redirectToRoute('logout', []);
                } else {

                    echo ("<script>console.log('from profileController: пароль неверный');</script>");
                    $error = true;
                    $this->info->setBody("Старый пароль указан неверно");
                }
            } catch (Exception $exception) {
                $error = true;
                $this->info->setBody("Неизвестная ошибка. Попробуйте позже");
            }
        }

        echo ("<script>console.log('from profileController: ".$del."');</script>");
        if (!$del) {


            $sc = $request->request->get('songcount');
            $passwordOld = $request->request->get('passwordold');
            $passwordNew = $request->request->get('passwordnew');
            $newname = $request->request->get('newname');


            echo ("<script>console.log('from profileController: здесь');</script>");
            if ($sc != null && $sc != "") {

                echo ("<script>console.log('from profileController: внутри');</script>");
                try {
                    $songCount =(int)$sc;

                    echo ("<script>console.log('from profileController: ".$songCount."');</script>");
                    if ($songCount > 0 && $songCount < 20) {
                        $personForm->setSongCount($songCount);
                    } else {
                        $error = true;
                        $this->info->setBody("Кол-во песен должно быть больше 0, но меньше 20");
                    }
                } catch (Exception $ex) {

                    echo ("<script>console.log('from profileController: ".json_encode($ex)."');</script>");
                    $error = true;
                    $this->info->setBody("Пожалуйста, введите число");
                }
            }

            if ($passwordNew != null && $passwordOld != null && $passwordOld != "" && $passwordNew != "") {
                if (strlen($passwordNew) >= 6) {
                    $personForm->setPassword($passwordOld);
                    $personForm->setPwd2($passwordNew);
                } else {
                    $error = true;
                    $this->info->setBody("Новый пароль слишком короткий!");
                }
            }

            if ($newname != null && $newname != "") {
                if (strlen($newname) >= 2) {
                    $personForm->setName($newname);
                } else {
                    $error = true;
                    $this->info->setBody("Имя слишком короткое");
                }
            }

            $ans = $this->profileService->update($personForm, $person);

            if ($ans == ProfileService::$NOT_UPDATED) {
                $error = true;
                $this->info->setBody("Не удалось сохранить");
            }
            if ($ans == ProfileService::$UNKNOWN_ERROR) {
                $error = true;
                $this->info->setBody("Проблемы с сохранением. Попробуйте позже");
            }
            if ($ans == ProfileService::$INCORRECT_PWD) {
                $error = true;
                $this->info->setBody("Старый пароль указан неверно");
            }

        }
        if ($error) {
            if ($this->info->getBody() == null)
                $this->info->setBody($this->bodyDanger);
            $this->info->setHead(Alert::$HEAD_DANGER);
            $this->info->setColor(Alert::$COLOR_DANGER);
        } else {
            $this->info->setBody($this->bodySuccess);
            $this->info->setHead($this->headSuccess);
            $this->info->setColor(Alert::$COLOR_SUCCESS);
        }

        return $this->render('profile.html.twig', [
            'user' => $person,
            'title' => "Настройки",
            'info' => $this->info
        ]);
    }
}
