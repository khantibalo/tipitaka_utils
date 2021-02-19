<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="tipitaka_users", uniqueConstraints={@ORM\UniqueConstraint(name="username", columns={"username"})})
 * @ORM\Entity(repositoryClass="App\Repository\TipitakaTocRepository")
 */
class TipitakaUsers 
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $userid;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="allow_comments_html", type="boolean", nullable=false, options={"default"="0"})
     */
    private $allowcommentshtml;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="isenabled", type="boolean", nullable=false, options={"default"="1"})
     */    
    private $isenabled;
    
    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->isenabled;
    }
    
    /**
     * @param boolean $isenabled
     */
    public function setIsenabled($isenabled): self
    {
        $this->isenabled = $isenabled;
        
        return $this;
    }

    /**
     * @return boolean
     */
    public function isAllowcommentshtml()
    {
        return $this->allowcommentshtml;
    }

    /**
     * @param boolean $allowcommenthtml
     */
    public function setAllowcommentshtml($allowcommenthtml): self
    {
        $this->allowcommentshtml = $allowcommenthtml;
        
        return $this;
    }

    public function getUserid(): ?int
    {
        return $this->userid;
    }

    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
    
}
