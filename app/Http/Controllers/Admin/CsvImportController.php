<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SpreadsheetReader;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use DB;

class CsvImportController extends Controller
{

    private function remove_empty($array) {
      return array_filter($array, array( $this, '_remove_empty_internal') );
    }
    private function _remove_empty_internal($value) {
      return !empty($value) || $value === 0;
    }
    public function parse(Request $request) {

        $file = $request->file('csv_file');
        $request->validate([
            'csv_file' => 'mimes:csv,txt',
        ]);

        $path = $file->path();
        $hasHeader = $request->input('header', false) ? true : false;

        $reader = new SpreadsheetReader($path);
        $headers = $reader->current();
        $lines = [];

        while( $reader->valid() ) {
            $lines[] = $reader->next();
        }
        
        
        $lines = $this->remove_empty( $lines );

        $filename = str_random(10) . '.csv';
        $file->storeAs('csv_import', $filename);

        $modelName = $request->input('model', false);
        $fullModelName = "App\\" . $modelName;

        $model = new $fullModelName();
        $fillables = $model->getFillable();

        $redirect = url()->previous();

        $operation = $request->operation;
        $duplicatecheck = $request->duplicatecheck;
        $contact_type = $request->contact_type;

        return view('csvImport.parse_import', compact('headers', 'filename', 'fillables', 'hasHeader', 'modelName', 'lines', 'redirect', 'operation', 'duplicatecheck', 'contact_type'));

    }

    public function process(Request $request) {

        
        $filename = $request->input('filename', false);
        $path = storage_path('app/csv_import/' . $filename);

        $hasHeader = $request->input('hasHeader', false);

        $fields = $request->input('fields', false);
        $fields = array_flip(array_filter($fields));

        $operation = $request->operation;
        $duplicatecheck = $request->duplicatecheck;
        $contact_type = $request->contact_type;

        $modelName = $request->input('modelName', false);
        $model = "App\\" . $modelName;
        $modelobj = new $model();
        $table = $modelobj->getTable();

        $reader = new SpreadsheetReader($path);
        $insert = [];

        foreach($reader as $key => $row) {
            $modelobj = new $model(); // Let us initiate the model for each row.

            if ($hasHeader && $key == 0) {
                continue;
            }

            $tmp = [];
            foreach($fields as $header => $k) {
                $tmp[$header] = trim( htmlspecialchars( $row[ $k ] ) );
            }

            if(Schema::hasColumn($table, 'created_by_id')) {
                $tmp['created_by_id'] =  Auth::id();
            }
            
            $caninsert = true;
            if ( 'contacts' === $table ) {
                if ( empty( $tmp['email'] ) ) {
                    
                    $caninsert = false;
                } else if( empty( $tmp['first_name'] ) ) {
                    $caninsert = false;
                } else {
                    $check = $modelobj->where('email', '=', $tmp['email'])->first();
                    if ( ! $check ) {
                        $check = DB::table('contacts')->where('email', '=', $tmp['email'])->first();
                    }
                    if ( $check ) {
                        $caninsert = false;
                    }
                }    
                
                $delivery_address = array();
                $delivery_address['first_name'] = ( ! empty( $tmp['first_name'] ) ) ? $tmp['first_name'] : '';
                $delivery_address['last_name'] = ( ! empty( $tmp['last_name'] ) ) ? $tmp['last_name'] : '';
                $delivery_address['address'] =  ( ! empty( $tmp['address'] ) ) ? $tmp['address'] : '';
                $delivery_address['city'] =  ( ! empty( $tmp['city'] ) ) ? $tmp['city'] : '';
                $delivery_address['state_region'] = ( ! empty( $tmp['state_region'] ) ) ? $tmp['state_region'] : '';
                $delivery_address['zip_postal_code'] = ( ! empty( $tmp['zip_postal_code'] ) ) ? $tmp['zip_postal_code'] : '';
                $delivery_address['country_id'] =  ( ! empty( $tmp['country_id'] ) ) ? $tmp['country_id'] : '';
                $tmp['delivery_address'] = json_encode( $delivery_address );
                $name = $delivery_address['first_name'];
                if ( ! empty( $delivery_address['last_name'] ) ) {
                    $name .= ' ' . $delivery_address['last_name'];
                }
                $tmp['name'] = $name;

                $fulladdress = $delivery_address['address'];
                if ( ! empty( $delivery_address['city'] ) ) {
                    $fulladdress .= ', ' . $delivery_address['city'];
                }
                if ( ! empty( $delivery_address['state_region'] ) ) {
                    $fulladdress .= ', ' . $delivery_address['state_region'];
                }
                if ( ! empty( $delivery_address['country_id'] ) ) {
                    $fulladdress .= ', ' . getCountryname( $delivery_address['country_id'] );
                }
                if ( ! empty( $delivery_address['zip_postal_code'] ) ) {
                    $fulladdress .= ' - ' . $delivery_address['zip_postal_code'];
                }
                $tmp['fulladdress'] = $fulladdress;

                
                $tmp['language_id'] = DEFAULT_LANGUAGE;                
            }

            if ( ! empty( $duplicatecheck ) &&  ! in_array( $table, array( 'contacts' ) ) ) {
                $columns = explode(',', $duplicatecheck);
                if ( ! empty( $columns ) ) {
                    foreach ( $columns as $column ) {                        
                        if ( ! empty( $tmp[ $column ] ) ) {
                            $modelobj = $modelobj->where($column, $tmp[ $column ]);                       
                        }                        
                    }
                    $check = $modelobj->first();
                    
                    if ( $check ) {
                        $caninsert = false;
                    }
                }
            }
            if ( $caninsert ) {
                $insert[] = $tmp;
            }
        }
        
        if ( 'contacts' === $table ) {
            foreach ($insert as $insert_item) {
                $record_id = $model::insertGetId($insert_item);

                $record = $model::find( $record_id );
                
                if ( empty( $contact_type ) ) {
                    $contact_type = DEFAULT_CONTACT_TYPE;
                }
                $contact_types = array( $contact_type );
                $record->contact_type()->sync( $contact_types );

                $languages = array( DEFAULT_LANGUAGE );
                $record->language()->sync($languages);
            }
        } else {
            $for_insert = array_chunk($insert, 100);

            foreach ($for_insert as $insert_item) {
                $model::insert($insert_item);
            }
        }

        $rows = count($insert);
        

        File::delete($path);

        $table = str_plural($modelName);

        $redirect = $request->input('redirect', false);
        return redirect()->to($redirect)->with('message', trans('global.app_imported_rows_to_table', ['rows' => $rows, 'table' => $table]));

    }

    public function downloadTemplate( $path, $folder = 'csvtemplates' ) {
        $path = public_path() . '/' . $folder . '/' . $path;
        return response()->download($path);
    }

    public function cleanString( $string ) {
       $string = str_replace(' ', '_', $string); // Replaces all spaces with hyphens.

       return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }

}
