<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Tests\Form\Type;

use Waldo\OpenIdConnect\ProviderBundle\Form\Type\ScopeApprovalType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * ScopeApprovalType
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class ScopeApprovalTypeTest extends TypeTestCase
{

    public function testBuildForm()
    {
        $type = new ScopeApprovalType();
        
        $form = $this->factory->create($type);
        
        $this->assertInstanceOf("Symfony\Component\Form\SubmitButton", $form->get("accept"));
        $this->assertInstanceOf("Symfony\Component\Form\SubmitButton", $form->get("accept"));
        
    }

    public function testGetName()
    {
        $type = new ScopeApprovalType();
        
        $this->assertEquals("scope_approval", $type->getName());
    }

}
