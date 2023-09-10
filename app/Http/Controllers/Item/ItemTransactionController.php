<?php

namespace App\Http\Controllers\Item;

use App\Helpers\MyLib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ItemTransactionController extends Controller
{
    private $auth;
    public function __construct()
    {
        $this->auth = MyLib::user();
    }

    public function index(Request $request)
    {
        MyLib::checkScope($this->auth,['ap-item_transaction-view']);

        
    }
}
