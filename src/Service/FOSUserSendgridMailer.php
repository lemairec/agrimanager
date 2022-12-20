<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

/**
 * @author Christophe Coevoet <stof@notk.org>
 */
class FOSUserSendgridMailer implements MailerInterface
{
    protected $mailer;

    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    /**
     * @var Environment
     */
    protected $twig;

    /**
     * @var array
     */
    protected $parameters;

    public function __construct(\Symfony\Component\Mailer\MailerInterface $mailer, UrlGeneratorInterface $router, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->twig = $twig;
    }

    /**
     * {@inheritdoc}
     */
    public function sendConfirmationEmailMessage(UserInterface $user)
    {
        $template = "%fos_user.resetting.email.template%";
        $url = $this->router->generate('fos_user_registration_confirm', ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);

        $context = [
            'user' => $user,
            'confirmationUrl' => $url,
        ];

        $this->sendMessage($template, $context, "%fos_user.registration.confirmation.from_email%", (string) $user->getEmail());
    }

    /**
     * {@inheritdoc}
     */
    public function sendResettingEmailMessage(UserInterface $user)
    {
        $template = "%fos_user.resetting.email.from_email%";
        $url = $this->router->generate("fos_user_resetting_reset", ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);

        $context = [
            'user' => $user,
            'confirmationUrl' => $url,
        ];

        $this->sendMessage("bundles/FOSUserBundle/Resetting/email.txt.twig", $context, "%fos_user.registration.confirmation.from_email%", (string) $user->getEmail());
    }

    /**
     * @param string $templateName
     * @param array  $context
     * @param array  $fromEmail
     * @param string $toEmail
     */
    protected function sendMessage($templateName, $context, $fromEmail, $toEmail)
    {
        $template = $this->twig->load($templateName);


        $subject = $template->renderBlock('subject', $context);
        $textBody = $template->renderBlock('body_text', $context);

        dump($subject);
        dump($textBody);

        $htmlBody = '';

        dump($toEmail);
        $message = (new Email())
            ->subject($subject)
            ->from('noreply@maplaine.fr')
            ->to($toEmail)
            ->text($textBody);

            dump($message);

        $this->mailer->send($message);

        echo $template2->toto;
    }
}
