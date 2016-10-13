<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// src/Ens/TestBundle/Security/MessageDigestPasswordEncoder.php
 
namespace OSSystem\EMTBundle\Security;
 
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder as BaseMessageDigestPasswordEncoder;

class MessageDigestPasswordEncoder extends BaseMessageDigestPasswordEncoder
{
    private $algorithm;
    private $encodeHashAsBase64;
 
    public function __construct($algorithm = 'sha512', $encodeHashAsBase64 = true, $iterations = 5000)
    {
        $this->algorithm = $algorithm;
        $this->encodeHashAsBase64 = $encodeHashAsBase64;
        $this->iterations = $iterations;
    }
 
    protected function mergePasswordAndSalt($password, $salt)
    {
        
        if (empty($salt)) {
            return $password;
        }

        switch($this->algorithm){
            case 'sha1':
                return $salt.$password;
            case 'sha512':
            default:    
                if (false !== strrpos($salt, '{') || false !== strrpos($salt, '}')) {
                    throw new \InvalidArgumentException('Cannot use { or } in salt.');
                }

                return $password.'{'.$salt.'}';
        }
        
    }
 
    public function encodePassword($raw, $salt)
    {
        if ($this->isPasswordTooLong($raw)) {
            throw new BadCredentialsException('Invalid password.');
        }

        if (!in_array($this->algorithm, hash_algos(), true)) {
            throw new \LogicException(sprintf('The algorithm "%s" is not supported.', $this->algorithm));
        }

        $salted = $this->mergePasswordAndSalt($raw, $salt);
        $digest = hash($this->algorithm, $salted, true);

        // "stretch" hash
        for ($i = 1; $i < $this->iterations; $i++) {
            $digest = hash($this->algorithm, $digest.$salted, true);
        }
        
        return $this->encodeHashAsBase64 ? base64_encode($digest) : bin2hex($digest);
    }
}