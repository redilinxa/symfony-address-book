<?php

namespace AppBundle\Presentation\Controller;

use AppBundle\Application\Forms\PersonType;
use AppBundle\Application\Services\PersonService;
use AppBundle\Domain\Entity\Person;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PersonController
 * @package AppBundle\Presentation\Controller
 * @Route("/contact", name="contact_")
 */
class PersonController extends Controller
{
    /**
     * @var PersonService
     */
    private $personService;

    /**
     * PersonController constructor.
     * @param PersonService $personService
     */
    public function __construct(PersonService $personService)
    {
        $this->personService = $personService;
    }

    /**
     * @Route("/list", name="list")
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $table = $this->personService->fetch($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        };

        return $this->render('addressBook/list.html.twig', ['datatable' => $table]);
    }

    /**
     * @Route("/create", name="create")
     * @param Request $request
     * @return Response
     * @throws OptimisticLockException
     */
    public function createAction(Request $request){
        $form = $this->createForm(PersonType::class, new Person());

        $person = $this->personService->save($request);

        if($person){
            $this->addFlash('success', 'Successfully created');
            return $this->redirectToRoute("contact_edit", ["id" => $person->getId()]);
        }

        return $this->render('addressBook/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     * @param Request $request
     * @param $id
     * @return Response
     * @throws OptimisticLockException
     */
    public function updateAction(Request $request, $id){
        $person = $this->personService->find($id);

        if(!$person){
            throw $this->createNotFoundException("Not Found");
        }

        $form = $this->createForm(PersonType::class, $person);

        $update = $this->personService->update($person, $request);

        if($update){
            $this->addFlash('success', 'Successfully updated');
            return $this->redirectToRoute("contact_edit", ["id" => $person->getId()]);
        }

        return $this->render('addressBook/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param $id
     * @return Response
     * @Route("/show/{id}", name="show")
     */
    public function showAction($id){
        $person = $this->personService->find($id);


        if(!$person){
            throw $this->createNotFoundException("Not Found");
        }

        return $this->render('addressBook/show.html.twig', [
            'person' => $person,
        ]);
    }


    /**
     * @Route("/delete", name="delete")
     * @param Request $request
     * @return JsonResponse
     * @throws OptimisticLockException
     */
    public function deleteAction(Request $request){

        if(!$id = $request->get("user")){
            return new JsonResponse([
                "success" => false,
                "message" => "Bad Request"
            ], 400);
        }

        $person = $this->personService->delete($id);

        if(!$person){
            return new JsonResponse([
                "success" => false,
                "message" => "Person not found"
            ], 202);
        }

        return new JsonResponse([
            "success" => true,
            "message" => "Successfully deleted"
        ], 200);
    }
}
