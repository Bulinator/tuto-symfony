<?php

namespace App\Email;

use App\Entity\User;
use Swift_Message;
use Twig\Environment;

class Mailer
{
    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * Mailer constructor.
     * @param \Swift_Mailer $mailer
     * @param Environment $twig
     */
    public function __construct(\Swift_Mailer $mailer, Environment $twig)
    {

        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendConfirmationEmail(User $user)
    {
        $body = $this->twig->render(
            'Email/confirmation.html.twig',
            [
                'user' => $user
            ]
        );

        // Send email here
        $message = (new Swift_Message("Please confirm your account"))
            ->setFrom('api-platform@api.com')
            ->setTo($user->getEmail())
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }
}