<?php

namespace App\Http\Controllers\Install;

use Illuminate\Http\Request;
use App\Http\Requests\StoreDatabaseDetails;
use App\Http\Requests\StoreAdminDetails;
use Storage;
use Artisan;
use Response;
use Redirect;
use Validator;
use Session;
use Hash;
use Auth;
use App\Models\SocialAccount;
use App\Models\User;
use App\Http\Controllers\Controller;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class InstallController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        ##$this->middleware('auth');
    }

    public function createUpdate() {

      //create zip of current files

      //compare with new versions

      //run migrations

      ini_set('max_execution_time', 180);

      Artisan::call('cache:clear');
      Artisan::call('view:clear');

      //create zip
      chdir(base_path());
      @mkdir('../warbler_installer/package/upload/');
      $str = 'zip -r9 ../warbler_installer/package/upload/warbler.zip ./* -x node_modules/**\* .git/**\* public/storage/**\* cgi-bin/**\* resources/ffmpeg/windows/* resources/ffmpeg/darwin/* storage/app/**\* storage/encodes/**\* storage/framework/cache/**\* storage/framework/sessions/**\* storage/framework/testing/**\* storage/framework/views/**\* storage/dotenv-editor/**\* storage/app/**\* *.log vendor/mgp25/instagram-php/sessions/**\* -DF --out ../warbler_installer/package/upload/warbler-update.zip';
      exec($str, $r);

      $zip = new \ZipArchive();
      if ($zip->open('../warbler_installer/package/upload/warbler-update.zip') === TRUE) {
          $zip->deleteName('.env');
          $zip->close();
          echo 'ok';
      } else {
          echo 'failed';
      }

    }

    public function upgrade() {
        Artisan::call('migrate');
        sleep(5); //sleep just incase it does it so fast people think nothing happened
        die("DONE - UPGRADED DATABASE");
    }

    public function create() {
      ini_set('max_execution_time', 180);

      Artisan::call('cache:clear');
      Artisan::call('view:clear');

      exec("gitbook build ../warbler_book/docs ../warbler_installer/docs");

  		//create zip
  		chdir(base_path());
  		@mkdir('../warbler_installer/package/upload/');
  		@unlink('../warbler_installer/package/upload/warbler.zip');
  		$str = 'zip -r9 ../warbler_installer/package/upload/warbler.zip ./* -x node_modules/**\* .git/**\* public/storage/**\* cgi-bin/**\* resources/ffmpeg/windows/* resources/ffmpeg/darwin/* storage/app/**\* storage/encodes/**\* storage/framework/cache/**\* storage/framework/sessions/**\* storage/framework/testing/**\* storage/framework/views/**\* storage/dotenv-editor/**\* storage/app/**\* *.log vendor/mgp25/instagram-php/sessions/**\*';
  		exec($str);

      $zip = new \ZipArchive();
      if ($zip->open('../warbler_installer/package/upload/warbler.zip') === TRUE) {
          $zip->deleteName('.env');
          $zip->renameName('.env.example', '.env');
          $zip->close();
          echo 'ok';
      } else {
          echo 'failed';
      }

      exec("gitbook pdf ../warbler_book/docs ../warbler_installer/package/manual.pdf");

  		die("DONE");
	 }

    public function index() {
        //Enter your mysql details
        return view('install.index');
    }

    public function postIndex(StoreDatabaseDetails $request) {
        //check if mysql is valid
        try {
          $connection = mysqli_connect(
            $request->get('database_host'),
            $request->get('database_username'),
            $request->get('database_password'),
            $request->get('database_name')
          );
        } catch(\Exception $e) {
          $validator = Validator::make([], []);
          $validator->getMessageBag()->add('mysql', 'There was an error connecting to the database. Please make sure the database credentials are correct.');
          return Redirect::back()->withErrors($validator)->withInput();
        }

        //save to env file
        @chmod(base_path("storage"), 0777);
        @chmod(resource_path('ffmpeg/linux/ffmpeg'), 0775);
        @chmod(resource_path('ffmpeg/linux/ffprobe'), 0775);
        $file = DotenvEditor::setKeys([
            [
                'key'     => 'DB_HOST',
                'value'   => $request->get('database_host'),
            ],
            [
                'key'     => 'DB_DATABASE',
                'value'   => $request->get('database_name'),
            ],
            [
                'key'     => 'DB_USERNAME',
                'value'   => $request->get('database_username'),
            ],
            [
                'key'     => 'DB_PASSWORD',
                'value'   => $request->get('database_password'),
            ]
        ]);
        $file = DotenvEditor::save();

        return redirect('install/admin');
    }

    public function admin() {
        Artisan::call('migrate');
        #Artisan::call('config:clear');
        #Artisan::call('config:cache');
        #dd(config(['database']));
        return view('install.admin');
    }

    public function postAdmin(StoreAdminDetails $request) {

        // Migrations and seeds
        try {
            #Artisan::call('key:generate');
            #Artisan::call('migrate');
            #Artisan::call('db:seed');
            Artisan::call('storage:link');

            Role::create(['name' => 'admin']);
            Role::create(['name' => 'client']);
        } catch(\Exception $e) {
            dd($e->getMessage());
        }

        //here we insert admin into the database
        $user = User::create([
           'name'     => $request->get('admin_username'),
           'email'    => $request->get('email'),
           'password' => Hash::make($request->get('admin_password')),
       ]);
       $user->assignRole('admin');

       //log user in
       Auth::login($user, true);

        return redirect('install/finish');
    }

    public function finish() {
        //Enter your mysql details
        return view('install.finish');

    }



}
