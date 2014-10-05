<?php

namespace Waldo\OpenIdConnect\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Waldo\OpenIdConnect\ModelBundle\Validator\Constraints\ConstraintExpressionLanguage;

/**
 * @ORM\Entity()
 * @ORM\Table(name="user_roles_rules")
 */
class UserRolesRules
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer", nullable=false, unique=true)
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="userRolesRulesList", cascade={"persist", "merge"})
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     * })
     *
     * @var Client $client 
     */
    protected $client;

    /**
     * @ORM\Column(name="expression", type="text", nullable=false)
     * @ConstraintExpressionLanguage(
     *  values = "Waldo\OpenIdConnect\ModelBundle\Expression\UserModel"
     * )
     * 
     * @var string $expression
     */
    protected $expression;

    /**
     * @ORM\Column(name="roles", type="array", nullable=false)
     * 
     * @var array $roles
     */
    protected $roles;

    /**
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     * 
     * @var boolean $enabled
     */
    protected $enabled = false;

    public function __construct()
    {
        
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return string
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param \Waldo\OpenIdConnect\ModelBundle\Entity\Client $client
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\UserRolesRules
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * 
     * @param string $expression
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\UserRolesRules
     */
    public function setExpression($expression)
    {
        $this->expression = $expression;
        return $this;
    }

    /**
     * 
     * @param array $roles
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\UserRolesRules
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * 
     * @param boolean $enabled
     * @return \Waldo\OpenIdConnect\ModelBundle\Entity\UserRolesRules
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

}
