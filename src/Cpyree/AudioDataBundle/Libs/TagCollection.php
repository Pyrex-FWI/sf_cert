<?php
namespace Cpyree\AudioDataBundle\Libs;

class TagCollection implements \Iterator, \Countable{
    var $tags = array();
    var $position = 0;
   
    public function __construct() {
        $this->position = 0;
    }


    public function append($items = array()){
        foreach($items as $item){
            $this->tags[] = $item;
        }
    }
    function rewind() {
        $this->position = 0;
    }

    function current() {
        return $this->tags[$this->position];
    }

    function key() {
        return $this->position;
    }

    function next() {
        ++$this->position;
    }

    function valid() {
        return isset($this->tags[$this->position]);
    }   
    public function add(Tag $t){
        $this->tags[] = $t;
    }

    public function __toString(){
    	$str = ""; $i = 1;
		foreach($this->tags as $tag){
			$str .= str_pad($i,strlen(count($this->tags)),'0',STR_PAD_LEFT) . ": ".$tag."\n";
			$i++;
		}
		return $str;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->tags);
    }
}
