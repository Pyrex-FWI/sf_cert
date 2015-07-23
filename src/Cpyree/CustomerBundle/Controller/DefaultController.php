<?php

namespace Cpyree\CustomerBundle\Controller;

use Cpyree\CustomerBundle\Entity\Customer;
use Cpyree\CustomerBundle\Form\Type\CustomerType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/customer/registration")
     * @Template("CpyreeCustomerBundle:Default:registration.html.twig")
     */
    public function indexAction()
    {
        $simpleRegistrationForm = $this->createForm(new CustomerType(), new Customer());
        return array(
            'simpleRegistrationForm'    =>  $simpleRegistrationForm->createView(),
            'customers'                 =>  $this->getDoctrine()->getRepository("CpyreeCustomerBundle:Customer")->findAll()
        );
    }

    /**
     * @param Request $request
     * @return array
     * @Route("customer/create")
     * @Template()
     */
    public function createAction(Request $request){

        $customer = new Customer();
        $form = $this->createForm(new CustomerType(), $customer);
        $form->handleRequest($request);
        if($form->isValid()){
            $factory = $this->get('security.encoder_factory');

            $encoder = $factory->getEncoder($customer);
            $password = $encoder->encodePassword($customer->getPassword(), "");
            $customer->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $customer->setCreated(new \DateTime());
            $customer->setUpdated(new \DateTime());
            $em->persist($customer);
            $em->flush();

        }
        return $this->redirect($this->generateUrl('cpyree_customer_default_index'));
    }
}
