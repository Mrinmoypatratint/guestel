<?php
namespace App\Http\Requests;
use App\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
class UploadHotelImageRequest extends FormRequest{
 public function authorize():bool{return (bool)$this->user()?->hasPermission('settings.manage',app(TenantContext::class)->id());}
 public function rules():array{return ['image'=>['required','file','mimetypes:image/jpeg,image/png,image/webp','max:8192','dimensions:max_width=5000,max_height=5000'],'category'=>['nullable','string','max:50'],'alt_text'=>['nullable','string','max:180'],'is_cover'=>['nullable','boolean']];}
}
