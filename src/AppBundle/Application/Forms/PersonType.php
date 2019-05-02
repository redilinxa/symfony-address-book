<?php


namespace AppBundle\Application\Forms;

use AppBundle\Domain\Entity\Country;
use AppBundle\Domain\Entity\Person;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PersonType
 * @package AppBundle\Application\Forms
 */
class PersonType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                "required" => true
            ])
            ->add('lastName', TextType::class, [
                "required" => true
            ])
            ->add('prefix',null, [
                "required" => true
            ])
            ->add('phoneNumber',null, [
                "required" => true
            ])
            ->add('birthday',TextType::class, [
                "required" => true
            ])
            ->add('email', EmailType::class, [
                "required" => true
            ])
            ->add('image', FileType::class, [
                "required" => false,
                "data_class" => null
            ])
            ->add('city',  EntityType::class, [
                "class" => Country::class,
                "required" => true,
                "group_by" => function($choice){
                    return $choice->getCountry() !== null ? $choice->getCountry()->getName() : "-";
                },
                "query_builder" => function(EntityRepository $er){
                    return $er->createQueryBuilder('c')
                        ->where("c.country IS NOT NULL");
                }
            ])
            ->add('address',  TextType::class)
            ->add('save', SubmitType::class)
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Person::class,
        ]);
    }
}