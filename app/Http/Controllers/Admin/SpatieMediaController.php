<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SpatieMediaController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        if (! $request->has('model_name') && ! $request->has('file_key') && ! $request->has('bucket')) {
            return abort(500);
        }

        $model_name = $request->input('model_name');
        if ( 'Modules' === substr( $model_name, 0,7) ) {
            $model = $request->input('model_name');
        } else {
            $model = 'App\\' . $request->input('model_name');
        }
        try {
            $model = new $model();
        } catch (ModelNotFoundException $e) {
            abort(500, 'Model not found');
        }

        $accept = $request->input('accept');
        if ( ! empty( $accept ) ) {
            $accept = explode(',', $accept);
        }

        $files      = $request->file($request->input('file_key'));
        $addedFiles = [];
        foreach ($files as $file) {
            try {
                $model->exists     = true;

                if ( empty( $accept ) ) {
                    $media             = $model->addMedia($file)->toMediaCollection($request->input('bucket'));
                    $addedFiles[]      = $media;
                } elseif ( ! empty( $accept ) && in_array( $file->getClientOriginalExtension(), $accept ) ) {
                    $media             = $model->addMedia($file)->toMediaCollection($request->input('bucket'));
                    $addedFiles[]      = $media;
                } else {
                    $addedFiles[]      = array( 'name' => $file->getClientOriginalName(), 'file_name' => $file->getClientOriginalName(), 'size' => 0);
                }
            } catch (\Exception $e) {
                abort(500, 'Could not upload your file');
            }
        }

        return response()->json(['files' => $addedFiles]);
    }
}
