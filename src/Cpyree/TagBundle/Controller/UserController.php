<?php
/**
 * Created by PhpStorm.
 * User: christophep
 * Date: 10/10/2014
 * Time: 23:39
 */

namespace Cpyree\TagBundle\Controller;

use Cpyree\TagBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class UserController
 * @package Cpyree\TagBundle\Controller
 * @Route("/user")
 *
 */
class UserController extends Controller{

    /**
     * @param Request $request
     * @Route(path="/add")
     * @Template()
     * @return array()
     */
    public function addAction(Request $request){
        $User = new User();
        $form = $this->createFormBuilder($User)
            ->add('username', 'text')
            ->add('password','password')
            ->add('email', 'email')
            ->add('add','submit')
            ->getForm();

        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if($form->isValid()){
            $em->persist($User);
            $em->flush();
        }
        $users = $em->getRepository("CpyreeTagBundle:User")->findAll();

        return array('users' =>$users, 'form'=>$form->createView());
    }
} 