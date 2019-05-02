<?php

namespace AppBundle\Application\Services;

use AppBundle\Application\Forms\PersonType;
use AppBundle\Application\Interfaces\CRUDInterface;
use AppBundle\Domain\Entity\Person;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Column\TwigColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableFactory;
use Psr\Container\ContainerInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PersonService
 * @package AppBundle\Application\Services
 */
class PersonService implements CRUDInterface
{
    /**
     * @var EntityManager|mixed
     */
    private $em;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var mixed|FormFactory
     */
    private $formFactory;

    /**
     * @var FileUploader
     */
    private $fileUploader;

    /**
     * PersonService constructor.
     * @param ContainerInterface $container
     * @param FileUploader $fileUploader
     */
    public function __construct(ContainerInterface $container, FileUploader $fileUploader)
    {
        $this->em = $container->get('doctrine.orm.entity_manager');
        $this->formFactory = $container->get('form.factory');
        $this->container = $container;
        $this->fileUploader = $fileUploader;
    }

    /**
     * @param Request $request
     * @return DataTable
     */
    public function fetch(Request $request)
    {
        $table = $this->container->get(DataTableFactory::class)->create()
            ->add('firstName', TextColumn::class, [
                "label" => "First Name"
            ])
            ->add('lastName', TextColumn::class, [
                "label" => "Last Name"
            ])
            ->add('birthDay', TextColumn::class, [
                "label" => "Birthday"
            ])
            ->add('email', TextColumn::class, [
                "label" => "Email"
            ])
            ->add('prefix', TextColumn::class, [
                "label" => "EXT",
            ])
            ->add('phoneNumber', TextColumn::class, [
                "label" => "Phone Number",
                "render"  => function($value, $context){
                    return "+ " . $context->getPrefix() . ' - ' . $value;
                }
            ])
            ->add('city', TextColumn::class, [
                "label" => "City",
                "render"  => function($value, $context){
                    return $context->getCity() ? $context->getCity()->getName() : $value;
                }
            ])
            ->add('actions', TwigColumn::class, [
                "template" => "addressBook/actions.html.twig",
                "label" => "Actions",
            ])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Person::class,
            ])->handleRequest($request);


        return $table;
    }


    /**
     * @param $id
     * @return object|null
     */
    public function find($id)
    {
       return $this->em->getRepository(Person::class)->find($id);
    }

    /**
     * @param $id
     * @return bool
     * @throws OptimisticLockException
     */
    public function delete($id)
    {
        $user = $this->find($id);

        if(!$user){
            return false;
        }

        $this->em->remove($user);
        $this->em->flush();
          return true;
    }

    /**
     * @param Request $request
     * @return Person|bool|mixed
     * @throws OptimisticLockException
     */
    public function save(Request $request){
        $person = new Person();
        $form = $this->formFactory->create(PersonType::class, $person);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $file = $person->getImage();

            $fileName = $this->fileUploader->upload($file);

            $person->setImage($fileName);

            $person = $form->getData();
            $this->em->persist($person);
            $this->em->flush();
            return $person;
        }

        return false;
    }

    /**
     * @param $person
     * @param Request $request
     * @return bool|mixed
     * @throws OptimisticLockException
     */
    public function update($person, Request $request)
    {
        $image = $person->getImage();
        $form = $this->formFactory->create(PersonType::class, $person);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if(!$person->getImage()){
                $person->setImage(new File($image));
            }
            $person = $form->getData();
            $this->em->persist($person);
            $this->em->flush();
            return $person;
        }

        return false;
    }
}