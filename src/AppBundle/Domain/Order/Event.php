<?php

namespace AppBundle\Domain\Order;

use AppBundle\Domain\Event as BaseEvent;
use AppBundle\Domain\SerializableEventInterface;
use AppBundle\Sylius\Order\OrderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Translation\TranslatorInterface;

abstract class Event extends BaseEvent implements SerializableEventInterface
{
    protected $order;

    public function __construct(OrderInterface $order)
    {
        $this->order = $order;
    }

    public function getOrder(): OrderInterface
    {
        return $this->order;
    }

    public function normalize(SerializerInterface $serializer)
    {
        $normalized = $serializer->normalize($this->getOrder(), 'jsonld', [
            'groups' => ['order', 'address', 'place']
        ]);

        return [
            'order' => $normalized,
            'data' => $event->toPayload(),
            // FIXME We should retrieve the actual date from EventStore
            'createdAt' => (new \DateTime())->format(\DateTime::ATOM),
        ];
    }

    public function forHumans(TranslatorInterface $translator, UserInterface $user = null)
    {
        $params = [
            '%aggregate_id%' => $this->getOrder()->getId(),
            '%owner%' => $user ? $user->getUsername() : '?',
        ];

        $key = 'activity.' . str_replace(':', '.', $this::messageName());

        return trim($translator->trans($key, $params));
    }
}
