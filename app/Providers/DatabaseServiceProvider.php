<?php

namespace App\Providers;

use Aws\SecretsManager\SecretsManagerClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Config;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if (!Cache::has('db-credentials')) {
            Cache::set('db-credentials', $this->getDbCredentials());
        }

        $this->setConfig(Cache::get('db-credentials'));

        try {
            DB::connection('main')->getPdo();
            DB::connection('addresses')->getPdo();
        }
        catch (\PDOException) {
            Cache::set('db-credentials', $this->getDbCredentials());
            $this->setConfig(Cache::get('db-credentials'));
            DB::connection('main')->getPdo();
            DB::connection('addresses')->getPdo();
        }
    }

    protected function setConfig($credentials)
    {
        config(['database.connections.main.username' => $credentials["username"]]);
        config(['database.connections.main.password' => $credentials["password"]]);
        config(['database.connections.addresses.username' => $credentials["username"]]);
        config(['database.connections.addresses.password' => $credentials["password"]]);
    }

    protected function getDbCredentials()
    {
        $client = new SecretsManagerClient([
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY')
            ],
            'version' => '2017-10-17',
            'region' => env("AWS_DEFAULT_REGION", "eu-west-1")
        ]);

        $secretResult = $client->getSecretValue([
            'SecretId' => env("DB_SECRET_NAME"),
        ]);

        if (isset($secretResult['SecretString'])) {
            $dbSecret = json_decode($secretResult['SecretString']);
        } else {
            $dbSecret = json_decode(base64_decode($secretResult['SecretBinary']));
        }

        return[
            'username' => $dbSecret->username,
            'password' => $dbPassword = $dbSecret->password
        ];
    }
}
