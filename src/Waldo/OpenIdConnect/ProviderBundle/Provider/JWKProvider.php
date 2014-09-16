<?php

namespace Waldo\OpenIdConnect\ProviderBundle\Provider;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;

/**
 * JWKProvider
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class JWKProvider
{
    
    protected $options;
    protected $cacheDir;
    private $jwkFileName = "oic-p.jwk";
    private $jwkFileFolder = "/waldo/OIC/jwk-cache/";
    
    public function __construct($option, $cacheDir)
    {
        $this->options = $option;
        $this->cacheDir = $cacheDir;
    }
    
    public function getPrivateKey()
    {
        if($this->hasJwk()) {
            return file_get_contents($this->options['private_key']);
        }
        return null;
    }
    
    public function getJwkResponse()
    {
        if($this->hasJwk()) {
            $jwkContent = $this->getJwkContent();
            
            $headers = array(
                    "Cache-Control" => "no-store",
                    "Pragma" => "no-cache",
                    'Content-Type'=> 'application/jwk'
                );
            
            return new Response($jwkContent, Response::HTTP_OK, $headers);
        }
        
        return new Response("No key found", Response::HTTP_NOT_FOUND);
    }
    
    public function hasJwk()
    {
        return $this->options['private_key'] !== null;
    }
    
    private function getJwkContent()
    {
        $fs = new Filesystem();
        
        if(!$fs->exists($this->cacheDir . $this->jwkFileFolder . $this->jwkFileName)) {
            $fs->mkdir($this->cacheDir . $this->jwkFileFolder);
            $this->makeCache();
        }
        
        return file_get_contents($this->cacheDir . $this->jwkFileFolder . $this->jwkFileName);
    }
    
    private function makeCache()
    {
        
        $jwkMaker = new \JOSE_JWKMaker(
                $this->options['private_key'],
                time(),
                \JOSE_JWK::JWK_USE_SIG
                );
        
        $content = $jwkMaker->makeJwkContent();
        
        file_put_contents($this->cacheDir . $this->jwkFileFolder . $this->jwkFileName, $content);
    }
}
