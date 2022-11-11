<?php


namespace Gsis;

Use \SoapClient;
use \SoapHeader;
Use \stdClass;

class VatDetails
{
    protected $user, $pass, $ownTaxid;

    protected ?string $proxy_address = null;
    protected ?int $proxy_port = null;
    
    /**
     * @return string|null
     */
    public function getProxyAddress(): ?string {
        return $this->proxy_address;
    }
    
    /**
     * @param string|null $proxy_address
     * @return VatDetails
     */
    public function setProxyAddress(?string $proxy_address): self {
        $this->proxy_address = $proxy_address;
        
        return $this;
    }
    
    /**
     * @return int|null
     */
    public function getProxyPort(): ?int {
        return $this->proxy_port;
    }
    
    /**
     * @param int|null $proxy_port
     * @return VatDetails
     */
    public function setProxyPort(?int $proxy_port): self {
        $this->proxy_port = $proxy_port;
        
        return $this;
    }
    
    public function __construct(string $user, string $pass, string $ownTaxId)
    {
        $this->user = $user;
        $this->pass = $pass;
        $this->ownTaxid = $ownTaxId;
    }

    public function fetch(string $remoteTaxId)
    {
        $soapOptions = ['trace' => true];
        if($this->getProxyAddress() !== null && $this->getProxyPort() !== null) {
            $soapOptions['proxy_host'] = $this->proxy_address;
            $soapOptions['proxy_port'] = $this->proxy_port;
        }
        
        $soap = new SoapClient('https://www1.gsis.gr/webtax2/wsgsis/RgWsPublic/RgWsPublicPort?WSDL',$soapOptions);
        $auth = new stdClass();
        $auth->UsernameToken = new stdClass();
        $auth->UsernameToken->Username = $this->user;
        $auth->UsernameToken->Password = $this->pass;
        $soap->__setSoapHeaders(new SoapHeader('http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd', 'Security', $auth, TRUE));
        return $soap->rgWsPublicAfmMethod([
            'afmCalledBy' => $this->ownTaxid,
            'afmCalledFor' => $remoteTaxId,
        ]);
    }

    public function fetchBasic(string $remoteTaxId)
    {
        return (array)$this->fetch($remoteTaxId)['RgWsPublicBasicRt_out'];
    }
}