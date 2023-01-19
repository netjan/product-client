<?php

namespace App\Form;

use App\Filter\ProductFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('stock', ChoiceType::class, [
                'choices' => $this->getStockChoices(),
                'choice_translation_domain' => false,
                'label' => 'Filtruj produkty',
                'placeholder' => 'Wszystkie produkty',
                'required' => false,
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => ProductFilter::class,
            'allow_extra_fields' => true,
            'method' => 'GET',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }

    private function getStockChoices(): array
    {
        return [
            'Znajdują się na składzie' => 'true',
            'Nie znajdują się na składzie' => 'false',
            'Znajdują się na składzie w ilości większej niż 5' => '5',
        ];
    }
}
