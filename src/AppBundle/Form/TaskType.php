<?php

namespace AppBundle\Form;

use AppBundle\Entity\Address;
use AppBundle\Entity\Task;
use AppBundle\Service\TagManager;
use AppBundle\Service\TaskManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    private $tagManager;

    public function __construct(TagManager $tagManager)
    {
        $this->tagManager = $tagManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Pickup' => Task::TYPE_PICKUP,
                    'Dropoff' => Task::TYPE_DROPOFF,
                ],
                'expanded' => true,
                'multiple' => false,
                'disabled' => !$options['can_edit_type']
            ])
            ->add('address', AddressBookType::class, [
                'label' => 'form.task.address.label',
                'with_addresses' => $options['with_addresses']
            ])
            ->add('comments', TextareaType::class, [
                'label' => 'form.task.comments.label',
                'required' => false,
                'attr' => ['rows' => '2', 'placeholder' => 'form.task.comments.placeholder']
            ])
            ->add('doneAfter', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm:ss',
                'required' => false
            ])
            ->add('doneBefore', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm:ss',
                'required' => true
            ]);

        if ($options['with_tags']) {
            $builder->add('tagsAsString', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Tags'
            ]);
        }

        if ($builder->has('tagsAsString')) {
            $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {

                $form = $event->getForm();
                $task = $event->getData();

                $tags = array_map(function ($tag) {
                    return $tag->getSlug();
                }, iterator_to_array($task->getTags()));

                $form->get('tagsAsString')->setData(implode(' ', $tags));
            });

            $builder->get('tagsAsString')->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {

                $task = $event->getForm()->getParent()->getData();

                $tagsAsString = $event->getData();
                $slugs = explode(' ', $tagsAsString);
                $tags = $this->tagManager->fromSlugs($slugs);

                $task->setTags($tags);
            });
        }

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {

            $form = $event->getForm();
            $task = $event->getData();

            if ($form->has('timeSlot')) {
                $timeSlot = $form->get('timeSlot')->getData();
                $timeSlot->getChoice()->apply($task, $timeSlot->getDate());
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Task::class,
            'can_edit_type' => true,
            'with_tags' => true,
            'with_addresses' => [],
        ));
    }
}
