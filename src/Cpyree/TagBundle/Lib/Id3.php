<?php

namespace Cpyree\TagBundle\Lib;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use GetId3\GetId3Core as GetId3;
use GetId3\Lib\Helper;
/**
 * Description of Reader
 *
 * @author christophep
 */
class Id3 {
    
    public $getID3;
    var $file;
    public $read = false;
    
    public function __construct($file = null){
        if($file) $this->file = $file;
        $this->getID3 = new GetId3();
        return $this;
    }
    
    public function isReadyToRead(){
        return $this->file? true : false;
    }
    
    public function read($file = null){
        if($file) $this->file = $file;
        
        if($this->read) return $this;
        
        $this->getID3
            ->setOptionMD5DataSource(true)
            ->setEncoding('UTF-8')
            ->analyze(trim($this->file));
        Helper::CopyTagsToComments($this->getID3->info);

        return $this;
         
    }
    
    public function getTags(){
        return $this->getID3->info;
    }
    
    public function getImage(){
        
    }
    
    public function get($tagName, $id3Version = "id3v2"){
        $r = null;
        
        switch ($tagName) {
            case 'cover':
                $r = $this->getCover();
                break;

            default:
                if(isset($this->getID3->info['tags']['id3v2'][$tagName][0]) && $this->getID3->info['tags']['id3v2'][$tagName][0] !=""){
                    $r = $this->getID3->info['tags']['id3v2'][$tagName][0];
                }elseif(isset($this->getID3->info['tags']['id3v1'][$tagName][0])){
                    $r = $this->getID3->info['tags']['id3v1'][$tagName][0];
                }

                
                break;
        }
        
        return $r;
    }
    
    public function getCover(){
        $r = null;
        if(isset($this->getID3->info['id3v2']['APIC'][0]['data'])){
            $r = $this->getID3->info['id3v2']['APIC'][0]['data'];
        }
        return $r;
    }
    
    public function getCoverMime(){
        $r = null;
        if(isset($this->getID3->info['id3v2']['APIC'][0]['image_mime'])){
            $r = $this->getID3->info['id3v2']['APIC'][0]['image_mime'];
        }
        return $r;
    }
    
    public function getCoverExtension(){
        $sMime = $this->getCoverMime();
        $aMime = explode("/", $sMime);
        $extension = null;
        switch ($aMime[1]) {
            case 'jpeg':
                $extension = ".jpg";
                break;

            default:
                break;
        }
        return $extension;
    }
    
    public function getCoverHash(){
        return hash("md5", $this->getCover());
    }
    
    public function getEmbedCover(){
        if($p = $this->getCover()){
            $p = 'data:' . $this->getCoverMime() . ';base64,' . base64_encode($p);
            return $p;
        } 
        return null;
    }
    
    protected function serialize(){
        
        if(isset($this->getID3->info['comments']['picture'][0]['data'])){
            $this->getID3->info['comments']['picture'][0]['data'] = base64_encode(
                     $this->getID3->info['comments']['picture'][0]['data']
            );
        }
        
    }
}

