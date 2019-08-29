<?php

namespace AppBundle\Form;

use libphonenumber\PhoneNumberFormat;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RegistrationType extends AbstractType
{
    private $countryIso;
    private $isDemo;

    public function __construct($countryIso, $isDemo)
    {
        $this->countryIso = strtoupper($countryIso);
        $this->isDemo = $isDemo;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('givenName', TextType::class, array('label' => 'profile.givenName'))
            ->add('familyName', TextType::class, array('label' => 'profile.familyName'))
            ->add('telephone', PhoneNumberType::class, [
                'format' => PhoneNumberFormat::NATIONAL,
                'default_region' => strtoupper($this->countryIso),
                'label' => 'profile.telephone',
            ])
            ->add('save', SubmitType::class, array('label' => 'basics.save'))
            ->add('sendInvitation', SubmitType::class, array('label' => 'basics.send_invitation'));

        if ($this->isDemo) {
            $builder->add('accountType', ChoiceType::class, [
                'mapped' => false,
                'required' => true,
                'choices'  => [
                    'roles.ROLE_USER' => 'CUSTOMER',
                    'roles.ROLE_COURIER' => 'COURIER',
                    'roles.ROLE_RESTAURANT' => 'RESTAURANT',
                    'roles.ROLE_STORE' => 'STORE',
                ],
                'label' => 'profile.accountType'
            ]);
        }

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {

            $form = $event->getForm();

            $child = $form->get('username');

            $config = $child->getConfig();
            $options = $config->getOptions();
            $options['help'] = 'form.registration.username.help';

            $form->add('username', get_class($config->getType()->getInnerType()), $options);
        });
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }
}
