<?php

namespace Phpbr\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EventType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', [
                'label' => 'Nome do Evento'
            ])
            ->add('description', 'textarea', [
                'label' => 'Descrição',
                'attr' => [
                    'rows' => '15'
                ]
            ])
            ->add('location', 'text', ['label' => 'Localização'])
            ->add('day', 'text', [
                'label' => 'Dia'
            ])
            ->add('save', 'submit', [
                'label' => 'Salvar'
            ])
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Phpbr\AppBundle\Entity\Event'
        ]);
    }

    public function getBlockPrefix() {
        return 'phpbr_bundle_appbundle_event';
    }

    public function getName() {
        return $this->getBlockPrefix();
    }
}
