<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Environment;

class Mailer
{

    private $twig;
    private $mailer;
    private $params;

    public function __construct(Environment $twig, \Swift_Mailer $mailer, ParameterBagInterface $params)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->params = $params;
    }

    public function send($user, $subject, $template, $datas = [])
    {

        $from = $this->params->get('mailer_from');
        $from_name = $this->params->get('mailer_name_from');

        $message = (new \Swift_Message($subject))
            ->setFrom($from, $from_name)
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render($template, $datas),
                'text/html'
            );

        $this->mailer->send($message);
    }
}