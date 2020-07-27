<?php declare(strict_types=1);
require __DIR__.'/../vendor/autoload.php';

class AppUtility{

    public function initiateCommisionHandling($path){

        $jsonData = self::getFileContent($path);
        
        if(empty($jsonData)) return 'No data to process!';
        
        return self::processCommissionPayload($jsonData);
    }

    public function getFileContent($path){
        try{
            return file_get_contents($path);
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    function processCommissionPayload($payload){
    
        $payload = explode("\n",$payload);
    
        /*  Iterate payload items and handle their commissions */
        foreach($payload as $row){
            $entry = json_decode($row);
    
            $binValue   =   $entry->bin;
            $amountValue    =   $entry->amount;  
            $currency   =   $entry->currency;
    
            $bin = json_decode(self::initiateHTTPGet('https://lookup.binlist.net/' .$binValue));
    
            if(empty($bin)) throw new Exception('Bin results empty!');
        
            $isEu = self::isEu($bin->country->alpha2);
        
            /*  Gets rates for the respective entry's currency. */
            $rate = self::getRates($currency);

            /*  Calculate amounts and store the result in a variable. */
            $calculatedAmount =  self::calculateCommissionAmount($amountValue,$currency,$rate,$isEu);
    
            print $calculatedAmount."\n";
        }
        return 'Processed';
    }
    
    function calculateCommissionAmount($amount,$currency,$rate,$isEu){
        $amount = ($currency == 'EUR' or $rate == 0) ? $amount : $amount/$rate;
        return number_format($amount * $isEu,2);
    }
    
    function getRates($currency){
        return json_decode(self::initiateHTTPGet('https://api.exchangeratesapi.io/latest'), true)['rates'][$currency];
    }
    
    function initiateHTTPGet($endpoint,$inc_path = false){
        try{
            return file_get_contents($endpoint,$inc_path);
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    
    function isEu($c) {
        switch($c) {
            case 'AT':
            case 'BE':
            case 'BG':
            case 'CY':
            case 'CZ':
            case 'DE':
            case 'DK':
            case 'EE':
            case 'ES':
            case 'FI':
            case 'FR':
            case 'GR':
            case 'HR':
            case 'HU':
            case 'IE':
            case 'IT':
            case 'LT':
            case 'LU':
            case 'LV':
            case 'MT':
            case 'NL':
            case 'PO':
            case 'PT':
            case 'RO':
            case 'SE':
            case 'SI':
            case 'SK':
                return 0.01;
            default:
                return 0.02;
        }
    }
}