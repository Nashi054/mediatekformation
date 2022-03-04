<?php

namespace App\tests;

use App\Entity\Formation;
use PHPUnit\Framework\TestCase;

/**
 * Description of FormationTest
 *
 * @author Niels-Patrick
 */
class FormationTest extends TestCase {
    
    /**
     * Test que la méthode getPublishedAtString retourne un string correspondant
     * à la valeur de publishedAt de type DateTime
     */
    public function testGetPubishedAtString(){
        $formation = new Formation();
        $formation->setPublishedAt(new \DateTime('2022-02-25'));
        $this->assertEquals('25/02/2022', $formation->getPublishedAtString());
    }
}