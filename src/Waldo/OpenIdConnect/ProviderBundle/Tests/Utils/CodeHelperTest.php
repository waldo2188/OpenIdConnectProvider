<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Tests\Utils;

use Waldo\OpenIdConnect\ProviderBundle\Utils\CodeHelper;

/**
 * @group codeHelper
 */
class CodeHelperTest extends \PHPUnit_Framework_TestCase
{

    public function testShouldGenereateAValidHTTPParameter()
    {
        $value = "";
        for($x = 10; $x >= 0; $x--) {
            $value .= CodeHelper::generateCode();
        }

        foreach(array('.', '+', '~', '/') as $mustNotExist) {
            $this->assertFalse(stripos($value, $mustNotExist));
        }
        
    }
    
    public function testShouldGenereateATokenForBearer()
    {
        $value = "";
        for($x = 10; $x >= 0; $x--) {
            $value .= CodeHelper::generateCode(true);
        }

        $test = false;
        foreach(array('.', '+', '~', '/') as $mustExist) {
            $test |= stripos($value, $mustExist) !== false ? true : false;
        }
        $this->assertTrue((bool)$test);
    }

    public function testShouldCheckForExistingToken()
    {
        $entityRepo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
                ->disableOriginalConstructor()
                ->getMock()
                ;
        
        $count = 0;
        
        $entityRepo->expects($this->exactly(3))
                ->method("findOneBy")
                ->with($this->arrayHasKey("aPropertyName"))
                ->will($this->returnCallback(
                        function() use (&$count) {
                            $count++;
                            return $count < 3 ? array() : null;
                        }
                        ));
        

        $value = CodeHelper::generateUniqueCode($entityRepo, "aPropertyName");
        
        foreach(array('.', '+', '~', '/') as $mustNotExist) {
            $this->assertFalse(stripos($value, $mustNotExist));
        }
    }
    
}
