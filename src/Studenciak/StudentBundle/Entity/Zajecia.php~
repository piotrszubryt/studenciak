<?php

 // src/Studenciak/Entity/Zajecia.php

namespace Studenciak\StudentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Entity
* @ORM\Table(name="zajecia")
*/

class Zajecia
{
	/**
      * @ORM\Id
      * @ORM\Column(type="integer")
      * @ORM\GeneratedValue(strategy="AUTO")
      */
	protected $id_zajecia;
	

     /**
      * @ORM\Column(type="string", length=255)
      */
	protected $temat;
	

     /**
      * @ORM\Column(type="datetime")
      */
	protected $data;
	


    /**
     * Get id_zajecia
     *
     * @return integer 
     */
    public function getIdZajecia()
    {
        return $this->id_zajecia;
    }

    /**
     * Set temat
     *
     * @param string $temat
     * @return Zajecia
     */
    public function setTemat($temat)
    {
        $this->temat = $temat;

        return $this;
    }

    /**
     * Get temat
     *
     * @return string 
     */
    public function getTemat()
    {
        return $this->temat;
    }

    /**
     * Set data
     *
     * @param \DateTime $data
     * @return Zajecia
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return \DateTime 
     */
    public function getData()
    {
        return $this->data;
    }
}
