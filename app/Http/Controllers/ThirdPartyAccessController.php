<?php

namespace App\Http\Controllers;

use App\SecurityKey;
use App\SecurityKeyPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class ThirdPartyAccessController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {
        return view('settings.access',[
            "securityKeys" => SecurityKey::all(),
            "availablePermissions" => [
                "api.internal.resource.access",
                "api.internal.resource.access.multiple"
            ]
        ]);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store() {
        // Create new security key.
        $newSecurityKey = SecurityKey::create([
           "label" => Input::get('label'),
            "secret" => Input::get('secret'),
            "hash" => bcrypt(Input::get('secret'))
        ]);
        // Security key permissions.
        foreach(Input::get('permissions') as $item) {
            SecurityKeyPermission::create([
                "security_key_id" => $newSecurityKey->id,
                "action" => $item
            ]);
        }
        // Return a redirect
        return back();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id) {
        SecurityKey::whereId($id)->delete();
        return back();
    }
}
