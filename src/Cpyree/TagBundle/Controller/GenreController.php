<?php
/**
 * Created by PhpStorm.
 * User: chpyr
 * Date: 07/10/14
 * Time: 18:09
 */

namespace Cpyree\TagBundle\Controller;

use \Symfony\Bundle\FrameworkBundle\Controller\Controller;
use \Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use \Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Cpyree\TagBundle\Entity\Genre;

/**
 * Class GenreController
 * @package Cpyree\TagBundle\Controller
 * @Route("/genre")
 */
class GenreController extends Controller{

	/**
	 * @Template()
	 * @Route(path="/", name="genre_index")
	 * @return array
	 */
	public function indexAction(){
		$genreRepository = $this->getDoctrine()->getRepository('CpyreeTagBundle:Genre');
		$genres = $genreRepository->findAll();
		return array('genres' => $genres);
	}


	/**
	 * @Route(path="/create", name="genre_create")
	 * @Template()
	 * @param Request $request
	 */
	public function createAction(Request $request){
		$genre = new Genre();
		$form = $this->createFormBuilder($genre)
		->add('name', 'text')
		->add('save', 'submit', array('label' => 'Create new genre'))
		->getForm();
		$form->handleRequest($request);

		if($form->isValid()){
			$em = $this->getDoctrine()->getManager();
			$em->persist($genre);
			$em->flush();
			return $this->redirect($this->generateUrl('genre_index'));
		}
		$genreRepository = $this->getDoctrine()->getRepository('CpyreeTagBundle:Genre');
		$genres = $genreRepository->findAll();
		return array("form" => $form->createView(), 'genres' => $genres);

	}

} 