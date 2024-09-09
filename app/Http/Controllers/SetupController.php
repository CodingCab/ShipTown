<?php

namespace App\Http\Controllers;

use App\Modules\MagentoApi\src\Models\MagentoConnection;

class SetupController extends Controller
{
    public function magento()
    {
        if (MagentoConnection::exists()) {
            return redirect()->route('dashboard');
        }

        return view('setup.magento');
    }
}
