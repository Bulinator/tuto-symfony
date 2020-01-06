<?php

namespace App\Email;

use App\Entity\User;

class Mailer
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * Mailer constructor.
     * @param \Swift_Mailer $mailer
     * @param \Twig_Environment $twig
     */
    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig)
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
        $message = (new \Swift_Message("Hello from api platform (dev)"))
            ->setFrom('api-platform@api.com')
            ->setTo($user->getEmail())
            ->setBody($body);

        $this->mailer->send($message);
    }
}