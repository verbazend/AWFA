<?php

/**
 * Override to overcome problems with Startrack Self Signed SSL Certificates on
 * certain server configurations.
 *
 * The important options here that aren't available in the SoapClient options are
 * CURLOPT_SSLVERSION       - Forces the SSl Version to 3
 * CURLOPT_SSL_VERIFYHOST   - Tells ssl not to care that the Startrack SSL certificate is for a different domain
 * CURLOPT_SSL_VERIFYPEER   - Tells ssl not to care that the Startrack SSL certificate is from a bogus CA (I think)
 *
 */
class SoapClientCurl extends SoapClient
{
    /**
     *
     * @param string $request       - The XML SOAP request.
     * @param string $location      - The URL to request.
     * @param string $action        - The SOAP action.
     * @param int $version          - The SOAP version.
     * @param boolean $one_way      - If one_way is set to 1, this method returns nothing. Use this where a response is not expected.
     * @throws SoapFault
     * @return string|void
     */
    public function __doRequest($request, $location, $action, $version, $one_way = 0)
    {
        $handle = curl_init();

        curl_setopt($handle, CURLOPT_URL, $location);
        curl_setopt($handle, CURLOPT_HTTPHEADER, array(
                'Content-type: text/xml;charset="utf-8"',
                'Accept: text/xml',
                'Cache-Control: no-cache',
                'Pragma: no-cache',
                'SOAPAction: '.$action,
                'Content-length: '.strlen($request))
        );

        curl_setopt($handle, CURLOPT_RETURNTRANSFER,    true);
        curl_setopt($handle, CURLOPT_POSTFIELDS,        $request);
        curl_setopt($handle, CURLOPT_SSLVERSION,        3);
        curl_setopt($handle, CURLOPT_PORT,              443);
        curl_setopt($handle, CURLOPT_POST,              true );
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST,    false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER,    false);

        $response = curl_exec($handle);

        if(empty($response))
        {
            throw new SoapFault('CURL error: '.curl_error($handle), curl_errno($handle));
        }

        curl_close($handle);

        if(1 !== $one_way)
        {
            return $response;
        }
    }
}