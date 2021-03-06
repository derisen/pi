<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\ProfileKeyword;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * LoadProfileKeyword form.
 */
class LoadProfileKeyword extends Fixture implements DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em)
    {
        for($i = 0; $i < 4; $i++) {
            $fixture = new ProfileKeyword();
            $fixture->setName("name.{$i}");
            $fixture->setLabel("Name {$i}");
            $fixture->setProfileElement($this->getReference('profileelement.1'));
            
            $em->persist($fixture);
            $this->setReference('profilekeyword.' . $i, $fixture);
        }
        
        $em->flush();
        
    }
    
    /**
     * {@inheritdoc}
     */
    public function getDependencies() {
        // add dependencies here, or remove this 
        // function and "implements DependentFixtureInterface" above
        return [
            LoadProfileElement::class,
        ];
    }
    
        
}
