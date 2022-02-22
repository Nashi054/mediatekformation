<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\FormationRepository;
use App\Repository\NiveauRepository;

/**
 * Description of FormationsController
 *
 * @author emds
 */
class FormationsController extends AbstractController {
    
    private const PAGEFORMATIONS = "pages/formations.html.twig";

    /**
     *
     * @var FormationRepository
     */
    private $formationRepository;

    /**
     * 
     * @param FormationRepository $repository
     */
    function __construct(FormationRepository $formationRepository, NiveauRepository $niveauRepository) {
        $this->formationRepository = $formationRepository;
    }

    /**
     * @Route("/formations", name="formations")
     * @return Response
     */
    public function index(): Response{
        $formations = $this->formationRepository->findAll();
        return $this->render(self::PAGEFORMATIONS, [
            'formations' => $formations
        ]);
    }
    
    /**
     * @Route("/formations/tri/{champ}/{ordre}", name="formations.sort")
     * @param type $champ
     * @param type $ordre
     * @return Response
     */
    public function sort($champ, $ordre): Response{
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre);
        return $this->render(self::PAGEFORMATIONS, [
           'formations' => $formations
        ]);
    }   
        
    /**
     * @Route("/formations/recherche/{champ}", name="formations.findallcontain")
     * @param type $champ
     * @param Request $request
     * @return Response
     */
    public function findAllContain($champ, Request $request): Response{
        if($champ == "niveaux"){
            if($this->isCsrfTokenValid('filtre_niveaux', $request->get('_token'))){
                $valeur = $request->get("recherche");
                switch($valeur){
                    case "débutant":
                        $valeur = 1;
                        break;
                    case "confirmé":
                        $valeur = 2;
                        break;
                    case "expert":
                        $valeur = 3;
                        break;
                    default:
                        $valeur = "";
                        break;
                }
                $formations = $this->formationRepository->findByNiveaux($valeur);
                return $this->render(self::PAGEFORMATIONS, [
                    'formations' => $formations
                ]);
            }
            return $this->redirectToRoute("formations");
        }else{
            if($this->isCsrfTokenValid('filtre_'.$champ, $request->get('_token'))){
                $valeur = $request->get("recherche");
                $formations = $this->formationRepository->findByContainValue($champ, $valeur);
                return $this->render(self::PAGEFORMATIONS, [
                    'formations' => $formations
                ]);
            }
            return $this->redirectToRoute("formations");
        }
    }  
    
    /**
     * @Route("/formations/formation/{id}", name="formations.showone")
     * @param type $id
     * @return Response
     */
    public function showOne($id): Response{
        $formation = $this->formationRepository->find($id);
        return $this->render("pages/formation.html.twig", [
            'formation' => $formation
        ]);        
    }    
}