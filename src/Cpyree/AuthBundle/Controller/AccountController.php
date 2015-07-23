<?php
/**
 * Created by PhpStorm.
 * User: christophep
 * Date: 10/11/2014
 * Time: 14:40
 */

namespace Cpyree\AuthBundle\Controller;


use Cpyree\AuthBundle\Entity\Group;
use Cpyree\AuthBundle\Entity\User;
use Cpyree\AuthBundle\Form\Type\GroupeType;
use Cpyree\AuthBundle\Form\Type\UserRegistrationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AccountController
 * @package Cpyree\AuthBundle\Controller
 * @Route(path="/account")
 */
class AccountController extends Controller{


    /**
     * @Route(path="/", name="cpyree_auth_index")
     * @Template()
     */
    public function indexAction(){
        $this->getUser();
        return array(
            'userClass' =>  get_class($this->getUser())
        );
    }
    /**
     * @Route(path="/users", name="cpyree_auth_users")
     * @Template()
     */
    public function userAction(Request $request){


        $userForm = $this->createForm(new UserRegistrationType());

        $group = new Group();
        $groupForm = $this->createForm(new GroupeType(), $group, array(
            'action'    =>  $this->generateUrl('cpyree_auth_users')
        ));

        if($this->addGroup($groupForm, $request) === true) return $this->redirect($this->generateUrl('cpyree_auth_users'));
        //if($this->addUser($userForm, $request) === true) return $this->redirect($this->generateUrl('cpyree_auth_users'));;
        $this->addUser($userForm, $request);

        $users = $this->getDoctrine()->getRepository('CpyreeAuthBundle:User')->findAll();
        $groups = $this->getDoctrine()->getRepository('CpyreeAuthBundle:Group')->findAll();

        return array(
            'userClass' =>  get_class($this->getUser()),
            'users'     => $users,
            'groupForm' => $groupForm->createView(),
            'userForm'  => $userForm->createView(),
            'groups'    => $groups
        );
    }

    /**
     * @Route(path="/confirm_subscription/{token_hash}", name="cpyree_auth_confirm_subscribe")
     * @Template()
     */
    public function confirmSubscriptionAction($token_hash){
        $em =$this->getDoctrine()->getManager();
        $userrepo = $em->getRepository("CpyreeAuthBundle:User");
        /** @var User $user */
        $user = $userrepo->findOneByActivationToken($token_hash);
        if($user) {
            $user->setIsActive(true);
            $user->setActivationToken('');
            $em->persist($user);
            $em->flush();
            $message = \Swift_Message::newInstance()
                ->setSubject('Subscription')
                ->setFrom('noreply@cpyree.com')
                ->setTo($user->getEmail())
                ->setBody($this->renderView('CpyreeAuthBundle:Account:subscribtion_confirmation.txt.twig', array('user' => $user)))
            ;
            $this->get('mailer')->send($message);
            return $this->redirect($this->generateUrl('/'));
        }else{

        }
        die($user->getUsername());

    }

    /**
     * @param $groupForm
     * @param Request $request
     * @return bool
     */
    public function addGroup(&$groupForm, Request $request){

        $groupForm->handleRequest($request);

        if($groupForm->isSubmitted() === false) return;

        if($groupForm->isSubmitted() && $groupForm->isValid()){

            $em = $this->getDoctrine()->getManager();
            $em->persist($groupForm->getData());
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                'Le groupe a été ajouté'
            );
            return true;
        }else{
            $this->get('session')->getFlashBag()->add(
                'error',
                'Le groupe n\' a pas été ajouté!'
            );
        }
    }

    public function sendSubscriptionMail(User $user){
        $message = \Swift_Message::newInstance()
            ->setSubject('Subscription')
            ->setFrom('noreply@cpyree.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('CpyreeAuthBundle:Account:subscribtion.txt.twig', array('user' => $user)))
        ;
        $this->get('mailer')->send($message);
    }

    /**
     * @param $userForm
     * @param Request $request
     * @return bool
     */
    public function addUser(&$userForm, Request $request){


        $userForm->handleRequest($request);
        /** @var User $user */
        $user = $userForm->getData();
        if($userForm->isSubmitted() === false) return;

        if($userForm->isValid()){
            $em = $this->getDoctrine()->getManager();
            $factory = $this->get('security.encoder_factory');

            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
            $user->setPassword($password);
            $user->setActivationToken(md5(time()));
            $em->persist($user);
            $em->flush();

            $this->sendSubscriptionMail($user);
            $this->get('session')->getFlashBag()->add(
                'success',
                'L\'utilisateur a été ajouté'
            );
            return true;
        }else{
            $this->get('session')->getFlashBag()->add(
                'error',
                'L\'utilisateur n\' a pas été ajouté!'
            );
        }
    }

    /**
     * @Route(path="/add_group", name="cpyree_auth_add_group")
     * @Template()
     */
    public function addGroupAction(Request $request){

        $group = new Group();
        $groupForm = $this->createForm(new GroupeType(), $group);
        $groupForm->handleRequest($request);
        if($groupForm->isSubmitted() && $groupForm->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($group);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                'Le groupe a été ajouté'
            );
        }else{
            $this->get('session')->getFlashBag()->add(
                'error',
                'Le groupe n\' a pas été ajouté!'
            );
        }

        return $this->redirect($this->generateUrl('cpyree_auth_users'));

    }

    /**
     * @Route(path="/add_user", name="cpyree_auth_add_user")
     * @Template()
     */
    public function addUserAction(Request $request){

        $user = new User();
        $userForm = $this->createForm(new UserRegistrationType(), $user);
        $userForm->handleRequest($request);


        if($userForm->isSubmitted() && $userForm->isValid()){
            $em = $this->getDoctrine()->getManager();

            $em->persist($user);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                'L\'utilisateur a été ajouté'
            );
        }else{
            $this->get('session')->getFlashBag()->add(
                'error',
                'L\'utilisateur n\' a pas été ajouté!'
            );
        }

        return $this->redirect($this->generateUrl('cpyree_auth_users'));

    }

    /**
     * @Route(
     *  path="/remove_group/{id}",
     * name="cpyree_auth_remove_group",
     * requirements={"id":"\d+"})
     * @ParamConverter("group", class="CpyreeAuthBundle:Group")
     * @param Group $group
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeGroupAction(Group $group){
        $em = $this->getDoctrine()->getManager();
        $em->remove($group);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',
            'Le groupe a été supprimeé'
        );
        return $this->redirect($this->generateUrl('cpyree_auth_users'));
    }
    /**
     * @Route(
     *  path="/remove_user/{id}",
     * name="cpyree_auth_remove_user",
     * requirements={"id":"\d+"})
     * @ParamConverter("user", class="CpyreeAuthBundle:User")
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeUserAction(User $user){
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',
            'L\'utilisateur a été supprimeé'
        );
        return $this->redirect($this->generateUrl('cpyree_auth_users'));
    }
} 