<?php


namespace AppBundle\Application\Services;

use AppBundle\Application\Forms\CountryType;
use AppBundle\Application\Forms\PersonType;;
use AppBundle\Application\Interfaces\CRUDInterface;
use AppBundle\Domain\Entity\Country;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Column\TwigColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableFactory;
use Psr\Container\ContainerInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CountryService
 * @package AppBundle\Application\Services
 */
class CountryService implements CRUDInterface
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
     * CountryService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->em = $container->get('doctrine.orm.entity_manager');
        $this->formFactory = $container->get('form.factory');
        $this->container = $container;
    }

    /**
     * @param Request $request
     * @return mixed|DataTable
     */
    public function fetch(Request $request)
    {
        $table = $this->container->get(DataTableFactory::class)->create()
            ->add('name', TextColumn::class, [
                "label" => "Country",
                "render" => function($data, $row){
                    return  $row->getCountry() !== null ? $row->getCountry()->getName() :  "<b>" . $data ."</b>";
                }
            ])
            ->add('city', TextColumn::class, [
                "label" => "City",
                "render" => function($data, $row){
                    return  $row->getCountry() !== null ? $row->getName() : " - ";
                }
            ])
            ->add('zip', TextColumn::class, [
                "label" => "Zip",
                "render" => function($data, $row){
                    return  $row->getCountry() !== null ? $row->getZip() : " - ";
                }
            ])
            ->add('actions', TwigColumn::class, [
                "template" => "countriesCities/actions.html.twig",
                "label" => "Actions",
            ])

            ->createAdapter(ORMAdapter::class, [
                'entity' => Country::class,
            ])->handleRequest($request);

        return $table;

    }

    /**
     * @param $id
     * @return mixed|object|null
     */
    public function find($id)
    {
        return $this->em->getRepository(Country::class)->find($id);

    }

    /**
     * @param Request $request
     * @return Country|bool|mixed
     * @throws OptimisticLockException
     */
    public function save(Request $request)
    {
        $country = new Country();
        $form = $this->formFactory->create(CountryType::class, $country);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $country = $form->getData();
            $this->em->persist($country);
            $this->em->flush();
            return $country;
        }

        return false;
    }

    /**
     * @param $country
     * @param Request $request
     * @return bool|mixed
     * @throws OptimisticLockException
     */
    public function update($country, Request $request)
    {
        $form = $this->formFactory->create(CountryType::class, $country);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $country = $form->getData();
            $this->em->persist($country);
            $this->em->flush();
            return $country;
        }

        return false;
    }

    /**
     * @param $id
     * @return bool|mixed
     * @throws OptimisticLockException
     */
    public function delete($id)
    {
        $country = $this->find($id);

        if(!$country){
            return false;
        }

        $this->em->remove($country);
        $this->em->flush();
        return true;
    }
}