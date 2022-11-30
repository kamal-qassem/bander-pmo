<?php

namespace App\Http\Controllers\Admin;
use \App;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use PDO;
use Exception;
use Input;
use App\User;
use Validator;
use Artisan;

class InstallatationController extends Controller
{

  var $res = TRUE;
  public $conn = '';

  /**
   * This method is called for the first time is not database exists
   * @return [type] [description]
   */
    public function index(Request $request)
    {

        if ( file_exists(base_path() . '/bootstrap/cache/config.php') ) {
          unlink(base_path() . '/bootstrap/cache/config.php');
        }
        
        $data['title']              = 'Instructions';
        return view('install.index');
    }

    public function removeCache(Request $request)
    {
        
        flashMessage( 'danger', 'create', $validate['message']);
        return redirect()->route('install.requirements');
    }

    /**
   * This method is called for the first time is not database exists
   * @return [type] [description]
   */
    public function requirements(Request $request)
    {
        $steps = ['step1_active' => 'active', 'step2_active' => null, 'step3_active' => null];
        session()->put('steps', $steps);

        if ( $request->isMethod('post') ) {   
          $validate = validateRequirements();
          if ( ! $validate['status'] ) {
            flashMessage( 'danger', 'create', $validate['message']);
            return redirect()->route('install.requirements');
          } else {
            return redirect()->route('install.project');
          }
        }
        $data['title']              = 'Requirements';
        $data['steps'] = $steps;
        return view('install.requirements', $data);
    }


    public function installProject(Request $request)
    {
        $validate = validateRequirements();
        if ( ! $validate['status'] ) {
          flashMessage( 'danger', 'create', $validate['message']);
          return redirect()->route('install.requirements');
        }
        $steps = ['step1_active' => 'done', 'step2_active' => 'active', 'step3_active' => null];
        session()->put('steps', $steps);

        if ( $request->isMethod('post') ) {
          ini_set('max_execution_time', 300);
          $columns = array(
           'host_name'  => 'required',
           'database_name' => 'required',
           'user_name' => 'required',
          );
          $validator = Validator::make($request->all(), $columns);
          if ( ! $validator->passes() ) {
            return redirect()->back()->withErrors($validator)->withInput();
          }


          
          //Attempt to create database
          $message = $this->createDatabase($request);
          if (  $message != 1 ) {
            flashMessage( 'danger', 'create', $message);
            return redirect()->route('install.project');
          }

          //Attempt to load data to tables
          $message = $this->createTables($request);

         
          
          if (  $message != 1 ) {
            flashMessage( 'danger', 'create', $message);
            return redirect()->route('install.project');
          }

          //Attempt to set env variables
          $this->updateEnvironmentFile($this->prepareEnvData($request));
          

          flashMessage( 'success', 'create', 'Project Installed Successfully');

          if($request->has('sample_data'))
          {
            if($request->sample_data!='no-data')
            {
              return redirect()->route('login');
            }
          }

          return redirect()->route('install.register');
        }
        $data['title']              = 'Install';
        $data['steps'] = $steps;
        return view('install.database-details', $data);
    }

    public function defaultDataSeed( $request )
    {

    }

    public function registerUser(Request $request)
    {
        
        
        if ( $request->isMethod('post') ) {
          $columns = array(              
              'first_name' => 'required',
              'last_name' => 'required',
              'owner_email' => 'required|email|unique:contacts,email',
              'owner_password' => 'required|min:6',
          );
         $this->validate($request,$columns);

          // Everything is fine, create user to the system and redirect to login page
          $contact           = \App\Contact::where('email', $request->owner_email)->first();
          if ( $contact ) {
            flashMessage( 'danger', 'create', 'Email already exists.');
          } else {
            $user = new User();
            $name           = $request->first_name;
             if( $request->last_name != '' )
            $name .= ' ' . $request->last_name;
            $user->name       = $name;
            $user->email      = $request->owner_email;
            $user->password   = bcrypt($request->owner_password);
            $user->status     = "Active";
            
            $user->is_user = 'yes';

            $user->ticketit_admin = 1;
            $user->ticketit_agent = 1;
            $user->portal_language = 'en';
            $user->ticket_emails = 1;
            
            $user->save();
            $user->role()->sync([ADMIN_TYPE]);

            flashMessage( 'success', 'create', 'Admin user created successfully');
          }         
          return redirect()->route('login');
        }
        $data['title']              = 'Register user';        
        return view('install.first-user-after-install', $data);
    }

    // Function to the database and tables and fill them with the default data
  function createDatabase(Request $request)
  {
    $servername = $request->host_name;
    $username   = $request->user_name;
    $password   = $request->password;
    $database   = $request->database_name;
        
    try 
    {
        $this->conn = new PDO("mysql:host=$servername", $username, $password);
        // set the PDO error mode to exception
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->conn->exec("CREATE DATABASE IF NOT EXISTS `$database` ;") ;
        $this->conn = new PDO("mysql:host=$servername; dbname=$database", $username, $password);
        return 1;
    }
    catch(Exception $e)
    {
        $message = "Connection failed: " . $e->getMessage();
        return $message;
    }
  }


    function get_content($URL){
      
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_URL, $URL);
      $data = curl_exec($ch);
      curl_close($ch);
      return $data;
    }

  // Function to create the tables and fill them with the default data
  function createTables(Request $request)
  {
    try{
      
      if( $request->has('sample_data') && $request->sample_data=='data' ) {
        // Let us creaste all table and default data.
        $query = file_get_contents(public_path().'/install-scripts/install-withdata.sql');

        if ( ! empty( $query ) ) {
          $this->conn->exec($query);
          return 1;
        } else {
          $message = "Empty query";
          return $message;
        }
      } else {
        $query = file_get_contents(public_path().'/install-scripts/install-nodata.sql');

        if ( ! empty( $query ) ) {
          $this->conn->exec($query);
          return 1;
        } else {
          $message = "Empty query";
          return $message;
        }
      }
      
    } catch(Exception $ex) {
      $message = "Connection failed: " . $ex->getMessage();
      return $message;
    }
  }
  /**
   * This method will prepare database details data to update in env file
   * @param  Request $request [description]
   * @return [type]           [description]
   */
  public function prepareEnvData(Request $request)
  {
    $data['DB_HOST']    = $request->host_name;
    $data['DB_PORT']    = $request->port_number;
    $data['DB_DATABASE']  = $request->database_name;
    $data['DB_USERNAME']  = $request->user_name;
    $data['DB_PASSWORD']  = $request->password;
    $data['APP_URL'] = PREFIX1;
    return $data;
  }


  /**
     * This method updates the Environment File which contains all master settings
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    public function updateEnvironmentFile($data = array())
    {
      if( count($data) > 0 ) {
        $env = file_get_contents(base_path() . '/.env');
        $env = preg_split('/\s+/', $env);

        foreach((array)$data as $key => $value){
          // Loop through .env-data
          foreach($env as $env_key => $env_value){
            // Turn the value into an array and stop after the first split
            // So it's not possible to split e.g. the App-Key by accident
            $entry = explode("=", $env_value, 2);

            // Check, if new key fits the actual .env-key
            if($entry[0] == $key){
              // If yes, overwrite it with the new one
              $env[ $env_key ] = $key . "=" . $value;
            } else {
              // If not, keep the old one
              $env[ $env_key ] = $env_value;
            }
          }
          }
        $env = implode("\n", $env);
        file_put_contents(base_path() . '/.env', $env);
      }
      else
      {
        flashMessage( 'success', 'create', 'Please check your directory permissions');
        return redirect()->route('install.index');
      }
  }
}
