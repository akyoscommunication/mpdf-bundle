<?php
namespace Akyos\MpdfBundle\Response;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;

class PDFResponse extends Response
{
    public function __construct($content, $filename = null, int $status = 200, array $headers = [])
    {
        $headers = array_merge($headers, ['Content-Type' => 'application/pdf']);
        if(!is_null($filename)) {
            $headers['Content-Disposition'] = sprintf('attachment; filename="%s"', $filename);
        }
        parent::__construct($content, $status, $headers);
    }

}