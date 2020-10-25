<?php

namespace Cc\Bmsf\Controllers;

use Cc\Bmsf\Facades\Attacent;
use Illuminate\Http\Request;

class AttachmentController extends BaseController
{
    public function index(Request $request)
    {
        return succ(Attacent::getList(
            $request->input('page', 1),
            $request->input('type', 'image'),
            $request->input('filter', []),
        ));
    }

    public function store(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            if ($file->isValid()) {
                return succ(Attacent::upload($file));
            }
        }
        return err('the file does not exist or invalid');
    }

    public function destroy($id)
    {
        return succ(Attacent::delete($id));
    }
}
