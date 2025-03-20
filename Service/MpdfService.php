<?php

namespace Akyos\MpdfBundle\Service;

use ReflectionClass;
use Symfony\Component\HttpFoundation\Response;

class MpdfService {
	
    private bool $addDefaultConstructorArgs = true;

    /**
     * Get an instance of mPDF class
     * @param array $constructorArgs arguments for mPDF constror
     * @return \mPDF
     * @throws \ReflectionException
     */
    public function getMpdf(array $constructorArgs = []): \mPDF
    {
        $allConstructorArgs = $constructorArgs;
	    if($this->getAddDefaultConstructorArgs()) {
	        $allConstructorArgs = [array_merge(['utf-8', 'A4'], $allConstructorArgs)];
	    }		

        $reflection = new ReflectionClass('Mpdf\Mpdf');

        return $reflection->newInstanceArgs($allConstructorArgs);
    }	     
	
    /**
     * Returns a string which content is a PDF document
     * @param string $html Html content
     * @param array $argOptions Options for the constructor.
     * @return PDF file.
     */
    public function generatePdf(string $html, array $argOptions = []): PDF
    {
        //Calculate arguments
        $defaultOptions = [
            'constructorArgs'      => [],
            'writeHtmlMode'        => null,
            'writeHtmlInitialise'  => null,
            'writeHtmlClose'       => null,
            'outputFilename'       => '',
            'outputDest'           => 'S',
            'mpdf'                 => null
        ];
	
        $options = array_merge($defaultOptions, $argOptions);
        extract($options);

        if (null == $mpdf) {
            $mpdf = $this->getMpdf($constructorArgs);
        }

        //Add arguments to AddHtml function
        $writeHtmlArgs = [$writeHtmlMode, $writeHtmlInitialise, $writeHtmlClose];
        $writeHtmlArgs = array_filter($writeHtmlArgs, function($x) { 
            return !is_null($x); 
        });

        $writeHtmlArgs['html'] = $html;

        @call_user_func_array([$mpdf, 'WriteHTML'], $writeHtmlArgs);

        //Add arguments to Output function
        return $mpdf->Output($outputFilename, $outputDest);
    }

    /**
     * Generates an instance of Response class with PDF document
     * @param string $html
     * @param array $argOptions
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function generatePdfResponse(string $html, array $argOptions = []): Response
    {
        $response = new Response();		
        $response->headers->set('Content-Type', 'application/pdf');

        $content = $this->generatePdf($html, $argOptions);
        $response->setContent($content);

        return $response;
    }

    /**
     * Set the defaults argumnets to the constructor.
     * @param $val
     * @return \Akyos\MpdfBundle\Service\MpdfService
     */
    public function setAddDefaultConstructorArgs($val): static
    {
        $this->addDefaultConstructorArgs = $val;
        
        return $this;
    }
	
   /**
    * Get defaults arguntments.
    */
    public function getAddDefaultConstructorArgs(): bool
    {
	    return $this->addDefaultConstructorArgs;
    }
}
