<?php
/**
 * Created by PhpStorm.
 * User: christophep
 * Date: 06/10/2014
 * Time: 14:15
 */

namespace Acme\StoreBundle\Controller;


use Acme\StoreBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BaseController extends Controller{


    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getProductRepository(){
        return $this->getDoctrine()->getRepository('AcmeStoreBundle:Product');
    }

} 