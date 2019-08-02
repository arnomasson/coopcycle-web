<?php

namespace AppBundle\Form\Checkout;

use AppBundle\Form\AddressType;
use libphonenumber\PhoneNumberFormat;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Sylius\Bundle\PromotionBundle\Form\Type\PromotionCouponToCodeType;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\PromotionSubjectCoupon;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CheckoutAddressType extends AbstractType
{
    private $country;

    public function __construct($country)
    {
        $this->country = strtoupper($country);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('shippingAddress', AddressType::class, [
                'label' => false,
            ])
            ->add('notes', TextareaType::class, [
                'required' => false,
                'label' => 'form.checkout_address.notes.label',
                'help' => 'form.checkout_address.notes.help',
                'attr' => ['placeholder' => 'form.checkout_address.notes.placeholder']
            ])
            ->add('promotionCoupon', PromotionCouponToCodeType::class, [
                'label' => 'form.checkout_address.promotion_coupon.label',
                'required' => false,
            ])
            ->add('reusablePackagingEnabled', CheckboxType::class, [
                'required' => false,
                'label' => 'form.checkout_address.reusable_packaging_enabled.label',
            ]);

        $builder->get('shippingAddress')->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();

            // Disable streetAddress, postalCode & addressLocality
            $this->disableChildForm($form, 'streetAddress');
            $this->disableChildForm($form, 'postalCode');
            $this->disableChildForm($form, 'addressLocality');
        });

        // This listener adds a "telephone" field when the customer does not have telephone
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {

            $form = $event->getForm();
            $order = $event->getData();

            if (empty($order->getCustomer()->getTelephone())) {
                $form->add('telephone', PhoneNumberType::class, [
                    'format' => PhoneNumberFormat::NATIONAL,
                    'default_region' => $this->country,
                    'label' => 'form.checkout_address.telephone.label',
                    'mapped' => false
                ]);
            }
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {

            $form = $event->getForm();
            $order = $form->getData();

            $customer = $order->getCustomer();

            if ($form->has('telephone')) {
                $customer->setTelephone($form->get('telephone')->getData());
            }

            $shippingAddress = $order->getShippingAddress();

            // Copy customer data into address
            $shippingAddress->setFirstName($customer->getGivenName());
            $shippingAddress->setLastName($customer->getFamilyName());
            $shippingAddress->setTelephone($customer->getTelephone());
        });
    }

    private function disableChildForm(FormInterface $form, $name)
    {
        $child = $form->get($name);

        $config = $child->getConfig();
        $options = $config->getOptions();
        $options['disabled'] = true;

        $form->add($name, get_class($config->getType()->getInnerType()), $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('constraints', [
            new PromotionSubjectCoupon()
        ]);
    }
}
