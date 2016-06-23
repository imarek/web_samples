<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api")
 */
class ApiController extends Controller
{
    /**
     * @Route("/user/change-email/{userId}", name="api_change_user_email")
     * @Method("PUT")
     */
    public function userChangeEmailAction(Request $request, $userId)
    {
        $email = $request->get('email');

        if (null !== $email) {
            $result = $this->get('user_manager')->changeEmail($userId, $email);
        } else {
            $result = 'Parameter email is empty';
        }

        $response = [
            'result' => $result === true ? true : false,
            'message' => $result === true ? 'success' : $result,
        ];

        return new JsonResponse($response);
    }
}
