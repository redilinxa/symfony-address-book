<?php


namespace AppBundle\Application\Forms;

use AppBundle\Domain\Entity\Country;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CountryType
 * @package AppBundle\Application\Forms
 */
class CountryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('country', EntityType::class, [
                "class" => Country::class,
                "required" => false,
                "query_builder" => function(EntityRepository $er){
                    return $er->createQueryBuilder('c')
                        ->where("c.country IS NULL");
                },
                "placeholder" => false
            ])
            ->add('name', TextType::class, [
                "required" => true
            ])
            ->add("zip", TextType::class)
            ->add('save', SubmitType::class)
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Country::class,
        ]);
    }
}