<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\SiteIdentity;
use Illuminate\Http\Request;
use function redirect;
use function unlink;
use function view;

class SiteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('logout');
    }
    public function index()
    {
        $this->data['count'] = SiteIdentity::count();
        $this->data['siteIdentity'] = SiteIdentity::get();
        $this->data['site'] = SiteIdentity::get()->first();
        return view('backend.site.logoAndFooter', $this->data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'logo' => ['required', 'mimes:jpg,png,jpeg'],
            'footer_txt' => 'required',
            'copyright' => 'required'
        ]);
        $logo = $request->file('logo');
        $logo_ext = $logo->extension();
        $name_gen = hexdec(uniqid()) . '.' . $logo_ext;
        $last_image = 'uploads/site/' . $name_gen;
        $upload = 'uploads/site/';
        $site = new SiteIdentity();
        $site->logo = $last_image;
        $site->footer_txt = $request->footer_txt;
        $site->copyright = $request->copyright;
        if ($site->save()) {
            $logo->move($upload, $name_gen);
            $this->setSuccessMessage('Site Identity Added Successfully!');
            return redirect()->back();
        }
    }

    public function update(Request $request)
    {

        $logo = $request->file('logo');
        if ($logo) {
            $logo = $request->file('logo');
            $logo_ext = $logo->extension();
            $name_gen = hexdec(uniqid()) . '.' . $logo_ext;
            $last_image = 'uploads/site/' . $name_gen;
            $upload = 'uploads/site/';
            $site = SiteIdentity::find($request->id);
            $site->logo = $last_image;
            $site->footer_txt = $request->footer_txt;
            $site->copyright = $request->copyright;
            if ($site->save()) {
                $logo->move($upload, $name_gen);
                unlink($request->old_img);
                $this->setSuccessMessage('Site Identity Updated Successfully!');
                return redirect()->back();
            }
        } else {
            $site = SiteIdentity::find($request->id);
            $site->footer_txt = $request->footer_txt;
            $site->copyright = $request->copyright;
            if ($site->save()) {
                $this->setSuccessMessage('Site Identity Updated Successfully!');
                return redirect()->back();
            }
            return $request->all();
        }
    }
}
