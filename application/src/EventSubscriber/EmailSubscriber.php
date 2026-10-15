<?php

namespace App\EventSubscriber;

use App\Event\ExceptionGetSocialListEvent;
use App\Event\NoResponseGetSocialListEvent;
use App\Event\SendEmailEvent;
use App\Event\SuccessfulGetSocialListNotificationEvent;
use App\Repository\EmailRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailSubscriber implements EventSubscriberInterface
{
    private const INVALID_CERTIFICATE_ERROR = '-3';

    private array $emailsFrom;
    private array $emailsTo;
    private array $emailsForErrors;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param MailerInterface $mailer
     * @param EmailRepository $emailRepository
     * @param string $emailsFrom
     * @param string $emailSubject
     * @param string $emailErrorsSubject
     */
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private MailerInterface          $mailer,
        private EmailRepository          $emailRepository,
        string                           $emailsFrom,
        private string                   $emailSubject,
        private string                   $emailErrorsSubject,
    )
    {
        $this->emailsFrom = explode(',', $emailsFrom);
        $this->emailsTo = $this->emailRepository->findNotificationType();
        $this->emailsForErrors = $this->emailRepository->findErrorType();
    }

    public function onSuccessfulGetSocialList(SuccessfulGetSocialListNotificationEvent $event): void
    {
        $changesSocList = $event->getChangesSocList();

        if ($this->emailsForErrors) {
            $email = (new TemplatedEmail())
                ->from(...$this->emailsFrom)
                ->cc(...$this->emailsTo)
                ->subject($this->emailSubject)
                ->htmlTemplate('email/notification-message.twig')
                ->context([
                    'changesSocList' => $changesSocList,
                ]);

            $this->eventDispatcher->dispatch(new SendEmailEvent($email));

            $this->mailer->send($email);
        }
    }

    public function onExceptionGetSocialListEvent(ExceptionGetSocialListEvent $event): void
    {
        $code = $event->getResultCode();

        if ($code === self::INVALID_CERTIFICATE_ERROR) {
            $message = 'Запрос не выполнен. Срок действия сертификата Электронной Подписи истёк. Обратитесь к ответственному сотруднику.';
        } else {
            $message = 'Запрос не выполнен. Сбой в работе Информационной Системы Роскомнадзора. Обратитесь к ответственному сотруднику.';
        }

        if ($this->emailsForErrors) {
            $email = (new Email())
                ->from(...$this->emailsFrom)
                ->cc(...$this->emailsForErrors)
                ->subject($this->emailErrorsSubject)
                ->html($message);

            $this->mailer->send($email);
        }
    }

    public function onNoResponseGetSocialListEvent(NoResponseGetSocialListEvent $event): void
    {
        if ($this->emailsForErrors) {
            $email = (new Email())
                ->from(...$this->emailsFrom)
                ->cc(...$this->emailsForErrors)
                ->subject($this->emailErrorsSubject)
                ->html('Запрос не выполнен. Сбой в работе Сервиса РКН. Обратитесь к ответственному сотруднику.');

            $this->mailer->send($email);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            SuccessfulGetSocialListNotificationEvent::class => 'onSuccessfulGetSocialList',
            ExceptionGetSocialListEvent::class => 'onExceptionGetSocialListEvent',
            NoResponseGetSocialListEvent::class => 'onNoResponseGetSocialListEvent',
        ];
    }
}
