<?php

namespace App\Controller\admin;

use App\Entity\Niveau;
use App\Repository\FormationRepository;
use App\Repository\NiveauRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur de la page "Niveaux" dans la partie admin
 *
 * @author Niels-Patrick
 */
class AdminNiveauxController extends AbstractController {
    
    private const PAGENIVEAUX = "admin/admin.niveaux.html.twig";

    /**
     *
     * @var NiveauRepository
     */
    private $niveauRepository;
    
    /**
     *
     * @var FormationRepository
     */
    private $formationRepository;
    
    /**
     *
     * @var EntityManagerInterface
     */
    private $om;
    
    /**
     * 
     * @param NiveauRepository $niveauRepository
     * @param FormationRepository $formationRepository
     * @param EntityManagerInterface $om
     */
    function __construct(NiveauRepository $niveauRepository, FormationRepository $formationRepository , EntityManagerInterface $om) {
        $this->niveauRepository = $niveauRepository;
        $this->formationRepository = $formationRepository;
        $this->om = $om;
    }
    
    /**
     * @Route("/admin/niveaux", name="admin.niveaux")
     * @return Response
     */
    public function index(): Response{
        $niveaux = $this->niveauRepository->findAll();
        return $this->render(self::PAGENIVEAUX, [
            'niveaux' => $niveaux
        ]);
    }
    
    /**
     * @Route("/admin/niveaux/suppr/{id}", name="admin.niveau.suppr")
     * @param Niveau $niveau
     * @return Response
     */
    public function suppr(Niveau $niveau): Response{
        $utilise = false;
        $formations = $this->formationRepository->findAll();
        
        foreach($formations as $formation){
            if($formation->getNiveaux()->getId() == $niveau->getId()){
                $utilise = true;
            }
        }
        
        if($utilise == false){
            $this->om->remove($niveau);
            $this->om->flush();
        }
        return $this->redirectToRoute("admin.niveaux");
    }
    
    /**
     * @Route("/admin/niveaux/ajout", name="admin.niveau.ajout")
     * @param Request $request
     * @return Response
     */
    public function ajout(Request $request): Response{
        $niveau = new Niveau();
        if($this->isCsrfTokenValid('filtre_niveau', $request->get('_token'))){
            $valeur = $request->get("ajout");
            if($valeur != ""){
                $niveau->setLabel($valeur);
                $this->om->persist($niveau);
                $this->om->flush();
            }
        }
        return $this->redirectToRoute("admin.niveaux");
    }
}