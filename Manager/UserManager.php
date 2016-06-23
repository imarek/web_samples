<?php

namespace AppBundle\Manager;

use AppBundle\Event\UserEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;
use \Exception;
use \InvalidArgumentException;

class UserManager
{

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    private $changeEmailListeners = [
        ['marketing_system', 'postRequest'],
        ['stats_system', 'postRequest'],
    ];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Change user email
     * @param string|int $userId
     * @param string $newEmail
     * @throws \Exception
     * @return boolean|string $result
     */
    public function changeEmail($userId, $newEmail)
    {
        $result = false;

        try {
            if (false === is_numeric($userId)) {
                throw new InvalidArgumentException('userId must be a number', 1);
            }

            $this->validateEmail($newEmail);

            $user = $this->container->get('user_repository')->findOne((int) $userId);

            if (null === $user) {

                throw new Exception(sprintf('Username with id %s not found.', $userId), 2);
            }

            $oldEmail = $user->getSetEmail($newEmail);
            $this->container->get('user_repository')->persist($user);

            $this->notify($this->changeEmailListeners, UserEvent::CHANGE_EMAIL);

            $this->container->get('logger')->info(sprintf('User[%s] changed email from [%s] into [%s]', $userId, $oldEmail, $newEmail));

            $result = true;
        } catch (Exception $e) {
            $this->container->get('logger')->error(sprintf('User[%s] change email failed: [%s]', $userId, $e->getMessage()));
            $result = $e->getMessage();
        }

        return $result;
    }

    private function validateEmail($email)
    {
        $emailConstraint = new EmailConstraint();

        $errors = $this->container->get('validator')->validateValue(
            $email,
            $emailConstraint
        );

        if (0 === count($errors)) {

            return;
        }

        throw new InvalidArgumentException(sprintf('%s is not valid email address', $email));
    }

    private function notify(array $listeners, $eventName)
    {
        $dispatcher = new ContainerAwareEventDispatcher($this->container);

        foreach ($listeners as $listener) {
            $dispatcher->addListenerService($eventName, $listener);
        }

        $dispatcher->dispatch($eventName);
    }
}
