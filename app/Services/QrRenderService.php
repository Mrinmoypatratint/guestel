<?php
namespace App\Services;
use chillerlan\QRCode\{QRCode,QROptions};
class QrRenderService {
 public function svg(string $url): string {
  $options=new QROptions; $options->outputBase64=false; $options->addQuietzone=true;
  return (new QRCode($options))->render($url);
 }
}
