<?php


class MyString
{
    /**
     * @var string
     */
    private $str;

    public function __construct(string $str = "")
    {
        $this->str = $str;
    }

    /**
     * @return int
     */
    public function length(){
        return strlen($this->str);
    }

    public function concat(MyString $o){
        $this->str .= $o->__toString();
        return $this;
    }

    /**
     * @param string $characterMask
     */
    public function trim($characterMask = " \t\n\r\0\x0B"){
        trim($this->str, $characterMask);
        return $this;
    }
    /**
     * @return string
     */
    public function __toString(){
        return $this->str;
    }
}

$str = new MyString(' a ');
echo $str;
echo $str->trim();