<?php
// src/AppBundle/Entity/LawWord.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="law_word")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LawWordRepository")
 */
class LawWord
{
    /**
     * Word's id
     * 
     * @ORM\Column(type="integer")
     * @ORM\Id
     */
    private $word_id;

    /**
     * Law's id
     * 
     * @ORM\Column(type="integer")
     * @ORM\Id
     */
    private $law_id;

    /**
     * Counter of word/laws relation
     * 
     * @ORM\Column(type="integer")
     */
    private $counter;


    /**
     * Set wordId
     *
     * @param integer $wordId
     *
     * @return LawWord
     */
    public function setWordId($wordId)
    {
        $this->word_id = $wordId;

        return $this;
    }

    /**
     * Get wordId
     *
     * @return integer
     */
    public function getWordId()
    {
        return $this->word_id;
    }

    /**
     * Set lawId
     *
     * @param integer $lawId
     *
     * @return LawWord
     */
    public function setLawId($lawId)
    {
        $this->law_id = $lawId;

        return $this;
    }

    /**
     * Get lawId
     *
     * @return integer
     */
    public function getLawId()
    {
        return $this->law_id;
    }

    /**
     * Set counter
     *
     * @param integer $counter
     *
     * @return LawWord
     */
    public function setCounter($counter)
    {
        $this->counter = $counter;

        return $this;
    }

    /**
     * Get counter
     *
     * @return integer
     */
    public function getCounter()
    {
        return $this->counter;
    }
}
