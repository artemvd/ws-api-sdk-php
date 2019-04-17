<?php


namespace Dokobit\Tests\Login;

use Dokobit\Login\Sc;
use Dokobit\QueryInterface;
use Dokobit\Tests\TestCase;

class ScTest extends TestCase
{
    public function testGetFields()
    {
        $method = new Sc(base64_encode($this->getCertificate()));

        $result = $method->getFields();

        $this->assertArrayHasKey('certificate', $result);
        $this->assertSame(base64_encode($this->getCertificate()), $result['certificate']);
    }

    public function testGetAction()
    {
        $method = new Sc('');
        $this->assertSame('sc/login', $method->getAction());
    }

    public function testGetMethod()
    {
        $method = new Sc('');
        $this->assertSame(QueryInterface::POST, $method->getMethod());
    }

    public function testHasValidationConstraints()
    {
        $method = new Sc('', '');
        $collection = $method->getValidationConstraints();

        $this->assertInstanceOf(
            'Symfony\Component\Validator\Constraints\Collection',
            $collection
        );
    }

    public function testCreateResult()
    {
        $method = new Sc('', '');
        $this->assertInstanceOf('Dokobit\Login\ScResult', $method->createResult());
    }

    private function getCertificate()
    {
        return '-----BEGIN CERTIFICATE-----
MIIEiTCCA3GgAwIBAgIQcoTqsNDhLKxSOrmuT0Wx8zANBgkqhkiG9w0BAQUFADBs
MQswCQYDVQQGEwJFRTEiMCAGA1UECgwZQVMgU2VydGlmaXRzZWVyaW1pc2tlc2t1
czEfMB0GA1UEAwwWVEVTVCBvZiBFU1RFSUQtU0sgMjAxMTEYMBYGCSqGSIb3DQEJ
ARYJcGtpQHNrLmVlMB4XDTEzMDkxOTA4NDUzNFoXDTE2MDkxODIwNTk1OVowga4x
CzAJBgNVBAYTAkVFMRswGQYDVQQKDBJFU1RFSUQgKE1PQklJTC1JRCkxGjAYBgNV
BAsMEWRpZ2l0YWwgc2lnbmF0dXJlMSgwJgYDVQQDDB9URVNUTlVNQkVSLFNFSVRT
TUVTLDUxMDAxMDkxMDcyMRMwEQYDVQQEDApURVNUTlVNQkVSMREwDwYDVQQqDAhT
RUlUU01FUzEUMBIGA1UEBRMLNTEwMDEwOTEwNzIwgZ8wDQYJKoZIhvcNAQEBBQAD
gY0AMIGJAoGBAMFo0cOULrm6HHJdMsyYVq6bBmCU4rjg8eonNnbWNq9Y0AAiyIQv
J3xDULnfwJD0C3QI8Y5RHYnZlt4U4Yt4CI6JenMySV1hElOtGYP1EuFPf643V11t
/mUDgY6aZaAuPLNvVYbeVHv0rkunKQ+ORABjhANCvHaErqC24i9kv3mVAgMBAAGj
ggFmMIIBYjAJBgNVHRMEAjAAMA4GA1UdDwEB/wQEAwIGQDCBmQYDVR0gBIGRMIGO
MIGLBgorBgEEAc4fAwMBMH0wWAYIKwYBBQUHAgIwTB5KAEEAaQBuAHUAbAB0ACAA
dABlAHMAdABpAG0AaQBzAGUAawBzAC4AIABPAG4AbAB5ACAAZgBvAHIAIAB0AGUA
cwB0AGkAbgBnAC4wIQYIKwYBBQUHAgEWFWh0dHA6Ly93d3cuc2suZWUvY3BzLzAd
BgNVHQ4EFgQUgYlFJ4mwwD0xwLoUQMEr1sz36BIwIgYIKwYBBQUHAQMEFjAUMAgG
BgQAjkYBATAIBgYEAI5GAQQwHwYDVR0jBBgwFoAUQbb+xbGxtFMTjPr6YtA0bW0i
NAowRQYDVR0fBD4wPDA6oDigNoY0aHR0cDovL3d3dy5zay5lZS9yZXBvc2l0b3J5
L2NybHMvdGVzdF9lc3RlaWQyMDExLmNybDANBgkqhkiG9w0BAQUFAAOCAQEAFJip
/LFTOIiyOPGRha5OCndXNtdZrqKkfmI989HDneXSgeHbidkaoICFMfsazHpU/0Q8
krovwW98YHjF7wnavXYzk4x8dJ0eJ5Zo2Pt66ITShcCUePCjsbKtdQnjavOU9Pxt
FMX2tfJYUNV0WwUW+0A9TZr3zoVbbd2KM/5AoK9JoxrYIi2F8f7V5Fj0rhzv5X3o
Gm1ShGmRhOXn481sJsAQR0xk6cPiZ+hfWhYjQDJisw+0ZY31mDraXSrAESymG4tk
0ucDmZjUomZjLlOpPEE68op6m3xoCs327o9ELxlQNm9gmXmPhkPuWDRGdyDPBy2m
rMD/lQ8E6S9dUFNUPg==
-----END CERTIFICATE-----';
    }
}
