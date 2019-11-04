<?php


namespace Gsis;

Use \SoapClient;
use \SoapHeader;
Use \stdClass;

class VatDetails
{
    protected $user, $pass, $ownTaxid;

    public function __construct(string $user, string $pass, string $ownTaxId)
    {
        $this->user = $user;
        $this->pass = $pass;
        $this->ownTaxid = $ownTaxId;
    }

    public function fetch(string $remoteTaxId)
    {
        $soap = new SoapClient('https://www1.gsis.gr/webtax2/wsgsis/RgWsPublic/RgWsPublicPort?WSDL',['trace' => true]);
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