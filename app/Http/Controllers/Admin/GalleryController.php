<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;use App\Http\Requests\UploadHotelImageRequest;use App\Models\HotelGallery;use App\Support\TenantContext;use Illuminate\Support\Facades\Storage;use Illuminate\Support\Str;
class GalleryController extends Controller{
 public function index(){return view('admin.gallery.index',['images'=>HotelGallery::orderBy('sort_order')->latest()->get()]);}
 public function store(UploadHotelImageRequest $request,TenantContext $tenant){$file=$request->file('image');$mime=$file->getMimeType();$ext=match($mime){'image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp',default=>abort(422,'Unsupported image type.')};$name=Str::uuid().'.'.$ext;$path=$file->storeAs('hotels/'.$tenant->requireId().'/gallery',$name,'public');if($request->boolean('is_cover'))HotelGallery::query()->update(['is_cover'=>false]);HotelGallery::create(['category'=>$request->input('category'),'path'=>$path,'alt_text'=>$request->input('alt_text'),'sort_order'=>(int)(HotelGallery::max('sort_order')??0)+1,'is_cover'=>$request->boolean('is_cover')]);return back()->with('status','Image uploaded securely.');}
 public function destroy(HotelGallery $image){Storage::disk('public')->delete($image->path);$image->delete();return back()->with('status','Image removed.');}
}
