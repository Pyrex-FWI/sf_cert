<?php
/**
 * Created by PhpStorm.
 * User: chpyr
 * Date: 09/10/14
 * Time: 13:08
 */

namespace Cpyree\TagBundle\Controller;


use Symfony\Component\Security\Core\SecurityContextInterface;

class SecurityController extends \Symfony\Bundle\FrameworkBundle\Controller\Controller{

	public function loginAction(\Symfony\Component\HttpFoundation\Request $request){
		$session = $request->getSession();

		if($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
			$error = $request->attributes->get(SecurityContextInterface::AUTHENTICATION_ERROR);
		}
		elseif( null !== $session && $session->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
			$error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
			$session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
		}else{
			$error = "";
		}

		$lastUsername = (null === $session) ? '' : $session->get(SecurityContextInterface::LAST_USERNAME);

		return $this->render(
			'CpyreeTagBundle:Security:login.html.twig',
			array(
				'last_username' =>	$lastUsername,
				'error'			=> $error
			));
	}

}