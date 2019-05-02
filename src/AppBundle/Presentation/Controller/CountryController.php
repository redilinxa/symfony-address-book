<?php


namespace AppBundle\Presentation\Controller;

use AppBundle\Application\Forms\CountryType;
use AppBundle\Application\Services\CountryService;
use AppBundle\Domain\Entity\Country;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CountryController
 * @package AppBundle\Presentation\Controller
 * @Route("/country", name="country_")
 */
class CountryController extends Controller
{
    /**
     * @var CountryService
     */
    private $countryService;

    /**
     * CountryController constructor.
     * @param CountryService $countryService
     */
    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    /**
     * @Route("/list", name="list")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function indexAction(Request $request)
    {
        $table = $this->countryService->fetch($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('countriesCities/list.html.twig', ['datatable' => $table]);
    }

    /**
     * @Route("/create", name="create")
     * @param Request $request
     * @return Response
     * @throws OptimisticLockException
     */
    public function createAction(Request $request){
        $form = $this->createForm(CountryType::class, new Country());

        $country = $this->countryService->save($request);

        if($country){
            $this->addFlash('success', 'Successfully created');
            return $this->redirectToRoute("country_create");
        }

        return $this->render('countriesCities/create.html.twig', [
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
        $country = $this->countryService->find($id);

        if(!$country){
            throw $this->createNotFoundException("Not Found");
        }

        $form = $this->createForm(CountryType::class, $country);

        $update = $this->countryService->update($country, $request);

        if($update){
            $this->addFlash('success', 'Successfully updated');
            return $this->redirectToRoute("country_edit", ["id" => $country->getId()]);
        }

        return $this->render('countriesCities/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete", name="delete")
     * @param Request $request
     * @return JsonResponse
     * @throws OptimisticLockException
     */
    public function deleteAction(Request $request){

        if(!$id = $request->get("object")){
            return new JsonResponse([
                "success" => false,
                "message" => "Bad Request"
            ], 400);
        }

        $person = $this->countryService->delete($id);

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