<?php

namespace App\Form;

use DateTimeImmutable;
use Doctrine\DBAL\Types\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;

class MonthSelectorFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $mesFiches = $options['data'];

        $builder
            ->add('selectedMonth', ChoiceType::class, [
                'choices' => $mesFiches,
                'label' => 'Selecter le mois',
                'choice_label' => function ($choice){
                    $date = DateTimeImmutable::createFromFormat('Ym', $choice->getMois()) ;
                    return $date->format('M-Y');
                }

                ]);


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}