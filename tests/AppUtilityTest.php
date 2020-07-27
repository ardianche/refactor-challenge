<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require './src/AppUtility.php';

final class AppUtilityTest extends TestCase
{
    public function testGetFileContent(){
        $file = 'input.txt';
        $this->assertFileExists($file);
    }

    public function testInitiateCommisionHandling(){
        $path = 'input.txt';
        $this->assertEquals(
            'Processed',
            AppUtility::initiateCommisionHandling($path)
        );
    }

    public function testProcessCommissionPayload(){
        $path = 'input.txt';
        $jsonPayload = AppUtility::getFileContent($path);
        $this->assertEquals(
            'Processed',
            AppUtility::processCommissionPayload($jsonPayload )
        );
    }

    public function testCalculateCommissionAmount(){
        $singularBin = explode("\n",AppUtility::getFileContent('input.txt'))[0];

        $bin = json_decode(AppUtility::initiateHTTPGet('https://lookup.binlist.net/' .$singularBin->bin));

        $isEu = AppUtility::isEu($bin->country->alpha2);
        $rate = AppUtility::getRates($singularBin->currency);

        $this->assertGreaterThanOrEqual(0, 
            AppUtility::calculateCommissionAmount($singularBin->amount,$singularBin->currency,$rate,$isEu)
        );
    }

    public function testGetRates(){
        $rate = AppUtility::getRates("EUR");
        $this->assertTrue($rate );
    }

    public function testInitiateHTTPGet(){
        $exchangeRates = AppUtility::initiateHTTPGet('https://api.exchangeratesapi.io/latest');
        $this->assertEquals(false, 
            empty($exchangeRates)
        );
    }

    public function testIsEu(){
        $this->assertEquals(
            0.01,
            AppUtility::isEu('AT')
        );

        $this->assertEquals(
            0.02,
            AppUtility::isEu('USA')
        );
    }
}

