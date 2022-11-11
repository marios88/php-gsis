<?php


namespace Gsis;

Use \SoapClient;
use \SoapHeader;
Use \stdClass;

class a39
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
        $soapOptions = ['soap_version' => SOAP_1_2,'trace' => true];
        if($this->getProxyAddress() !== null && $this->getProxyPort() !== null) {
            $soapOptions['proxy_host'] = $this->proxy_address;
            $soapOptions['proxy_port'] = $this->proxy_port;
        }
        
        $soap = new SoapClient('https://www1.gsis.gr/wsaade/VtWs39aFPA/VtWs39aFPA?wsdl', $soapOptions);
        $soap->vt39afpaVersionInfo();
        $auth = new stdClass();
        $auth->UsernameToken = new stdClass();
        $auth->UsernameToken->Username = $this->user;
        $auth->UsernameToken->Password = $this->pass;
        $soap->__setSoapHeaders(new SoapHeader('http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd', 'Security', $auth, TRUE));
        return $soap->vt39afpaSu1GetVatflag([
            'SU1_IN_REC' => [
                'supl_afm' => $this->ownTaxid,
                'buyer_afm' => $remoteTaxId
            ]
        ]);
    }

    public function fetchBasic(string $remoteTaxId)
    {
        $response = $this->fetch($remoteTaxId);
        return [
            'status' => ($response->su1_out_rec->buyer_normal_vat_system === 'Y' ? true : false), // english "Y'
            'message' => $response->message_rec->message_descr,
            'message_code' => $response->message_rec->message_code,
            'buyer_fullname' => $response->su1_out_rec->buyer_fullname,
            'vatId' => $response->su1_out_rec->buyer_afm,
            'normalVat' => $response->su1_out_rec->buyer_normal_vat_system,
        ];
    }
}