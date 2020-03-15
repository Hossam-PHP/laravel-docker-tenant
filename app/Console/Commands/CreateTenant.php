<?php

namespace App\Console\Commands;

use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;
use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository;
use Hyn\Tenancy\Environment;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Models\Website;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create {subdomain} {name} {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a Tenant with a subdomain, name and email. Example: php artisan tenant:create test "Test User" test@example.com';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();       
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {        
        $url_base = config('app.url_base');
        $subdomain = $this->argument('subdomain');
        $name = $this->argument('name');
        $email = $this->argument('email');
        $fqdn = "{$subdomain}.{$url_base}";
        
        // first check to make sure the tenant doesn't already exist
        if ( $this->tenantExists( $fqdn ) ) {
            // abort with an error
            $this->error( "A tenant with the subdomain '{$subdomain}' already exists." );
            return;
        }
        
        // if the tenant doesn't exist, we'll use the Tenancy package commands to create one
        $hostname = $this->createTenant( $fqdn );

        // swap the environment over to the hostname
        app( Environment::class )->hostname( $hostname );


        // create a new user
        $password = str_random();
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make( $password )
        ]);

        // return a success message to the console
        $this->info( "Tenant '{$name}' created for {$fqdn}");
        $this->info( "The user '{$email}' can log in with password {$password}");
    }

    private function tenantExists( $fqdn ) {
        // check to see if any Hostnames in the database have the same fqdn
        return Hostname::where( 'fqdn', $fqdn )->exists();
    }

    private function createTenant( $fqdn )
    {
        // first create the 'website'
        $website = new Website;
        app( WebsiteRepository::class )->create( $website );

        // now associate the 'website' with a hostname
        $hostname = new Hostname;
        $hostname->fqdn = $fqdn;
        app( HostnameRepository::class )->attach( $hostname, $website );

        return $hostname;
    }
}
