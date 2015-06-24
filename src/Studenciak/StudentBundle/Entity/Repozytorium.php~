<?php

 // src/Studenciak/Entity/Repozytorium.php

namespace Studenciak\StudentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
* @ORM\Entity
* @ORM\Table(name="repozytorium")
*/

class Repozytorium
{
	/**
      * @ORM\Id
      * @ORM\Column(type="integer")
      * @ORM\GeneratedValue(strategy="AUTO")
      */
    protected $id_repozytorium;
    

      /**
      * @ORM\ManyToOne(targetEntity="Przedmiot")
      * @ORM\JoinColumn(name="id_przedmiotu", referencedColumnName="id_przedmiotu")
      */

    protected $id_przedmiotu;
    
      /**
      * @ORM\ManyToOne(targetEntity="Osoba")
      * @ORM\JoinColumn(name="id_osoby", referencedColumnName="id_osoby")
      */
    protected $id_osoby;
    

     /**
      * @ORM\Column(type="string", length=512)
      */
    protected $nazwa;
    

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $path;

     /**
     * @Assert\File(maxSize="5242880")
     */
    public $file;

    public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path ? null : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw when displaying uploaded doc/image in the view.
        return 'repozytoria';
    }

    public function upload()
{
    // zmienna file może być pusta jeśli pole nie jest wymagane
    if (null === $this->file) {
        return;
    }

    // używamy oryginalnej nazwy pliku ale nie powinieneś tego robić
    // aby zabezpieczyć się przed ewentualnymi problemami w bezpieczeństwie

    // metoda move jako atrybuty przyjmuje ścieżkę docelową gdzie trafi przenoszony plik
    // oraz ścieżkę z której ma przenieś plik
    //$this->file->move($this->getUploadRootDir(), $this->file->getClientOriginalName());
    $this->file->move('/web/repozytoria', $this->file->getClientOriginalName());
    // ustaw zmienną patch ścieżką do zapisanego pliku
    $this->setPath($this->file->getClientOriginalName());

    // wyczyść zmienną file ponieważ już jej nie potrzebujemy
    $this->file = null;
}

    
    /**
     * Get id_repozytorium
     *
     * @return integer 
     */
    public function getIdRepozytorium()
    {
        return $this->id_repozytorium;
    }

    /**
     * Set nazwa
     *
     * @param string $nazwa
     * @return Repozytorium
     */
    public function setNazwa($nazwa)
    {
        $this->nazwa = $nazwa;

        return $this;
    }

    /**
     * Get nazwa
     *
     * @return string 
     */
    public function getNazwa()
    {
        return $this->nazwa;
    }

    /**
     * Set id_przedmiotu
     *
     * @param \Studenciak\StudentBundle\Entity\Przedmiot $idPrzedmiotu
     * @return Repozytorium
     */
    public function setIdPrzedmiotu(\Studenciak\StudentBundle\Entity\Przedmiot $idPrzedmiotu = null)
    {
        $this->id_przedmiotu = $idPrzedmiotu;

        return $this;
    }

    /**
     * Get id_przedmiotu
     *
     * @return \Studenciak\StudentBundle\Entity\Przedmiot 
     */
    public function getIdPrzedmiotu()
    {
        return $this->id_przedmiotu;
    }

    /**
     * Set id_osoby
     *
     * @param \Studenciak\StudentBundle\Entity\Osoba $idOsoby
     * @return Repozytorium
     */
    public function setIdOsoby(\Studenciak\StudentBundle\Entity\Osoba $idOsoby = null)
    {
        $this->id_osoby = $idOsoby;

        return $this;
    }

    /**
     * Get id_osoby
     *
     * @return \Studenciak\StudentBundle\Entity\Osoba 
     */
    public function getIdOsoby()
    {
        return $this->id_osoby;
    }


    /**
     * Set path
     *
     * @param string $path
     * @return Repozytorium
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }
}
