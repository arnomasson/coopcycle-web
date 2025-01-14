<?php

namespace AppBundle\Domain\Order\Event;

use AppBundle\Domain\DomainEvent;
use AppBundle\Domain\HasIconInterface;
use AppBundle\Domain\Order\Event;
use AppBundle\Entity\StripePayment;
use AppBundle\Sylius\Order\OrderInterface;

class OrderFulfilled extends Event implements DomainEvent, HasIconInterface
{
    private $payment;

    public static function messageName()
    {
        return 'order:fulfilled';
    }

    public function __construct(OrderInterface $order, ?StripePayment $payment = null)
    {
        parent::__construct($order);

        $this->payment = $payment;
    }

    public function getPayment()
    {
        return $this->payment;
    }

    public static function iconName()
    {
        return 'check';
    }
}

