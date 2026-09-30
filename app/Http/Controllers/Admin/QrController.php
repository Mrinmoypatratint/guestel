<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\QrCode;
use App\Services\QrRenderService;
use App\Support\TenantContext;
class QrController extends Controller {
 public function svg(QrCode $qr,QrRenderService $renderer,TenantContext $tenant){abort_unless((int)$qr->hotel_id===$tenant->requireId(),403);$url=url('/'.config('hospitality.qr_public_prefix','g').'/'.$qr->public_token);return response($renderer->svg($url),200,['Content-Type'=>'image/svg+xml','Content-Disposition'=>'inline; filename="qr.svg"','Cache-Control'=>'private, max-age=300']);}
}
