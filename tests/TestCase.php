<?php

namespace Xoco70\KendoTournaments\Tests;

use Orchestra\Database\ConsoleServiceProvider;
use Orchestra\Testbench\BrowserKit\TestCase as BaseTestCase;
use Xoco70\KendoTournaments\Models\Competitor;
use Xoco70\KendoTournaments\Models\Fight;
use Xoco70\KendoTournaments\Models\FightersGroup;
use Xoco70\KendoTournaments\TournamentsServiceProvider;

abstract class TestCase extends BaseTestCase
{
    const DB_HOST = '127.0.0.1';
    const DB_NAME = 'plugin';
    const DB_USERNAME = 'root';
    const DB_PASSWORD = '';

    protected $root;
    protected $baseUrl = "http://tournament-plugin.dev";

    protected function getPackageProviders($app)
    {
        return [TournamentsServiceProvider::class,
            ConsoleServiceProvider::class,];
    }

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->root = new \Illuminate\Foundation\Auth\User();
        $this->makeSureDatabaseExists();
        parent::setUp();

        $this->withFactories(__DIR__ . '/../database/factories');
    }

    private function makeSureDatabaseExists()
    {
        $this->runQuery('CREATE DATABASE IF NOT EXISTS ' . static::DB_NAME);
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'mysql');
        $app['config']->set('database.connections.mysql', [
            'driver' => 'mysql',
            'host' => $_SERVER['DB_HOST'] ?? static::DB_HOST,
            'database' => $_SERVER['DB_NAME'] ?? static::DB_NAME,
            'username' => $_SERVER['DB_USERNAME'] ?? static::DB_USERNAME,
            'password' => $_SERVER['DB_PASSWORD'] ?? static::DB_PASSWORD,
            'prefix' => 'ken_',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'strict' => false,
        ]);
    }


    /**
     * @param $query
     * return void
     */
    private function runQuery($query)
    {
        $dbUsername = static::DB_USERNAME;
        $dbPassword = static::DB_PASSWORD;
        $command = "mysql -u $dbUsername ";
        $command .= $dbPassword ? " -p$dbPassword" : '';
        $command .= " -e '$query'";
        exec($command . ' 2>/dev/null');
    }


    /**
     * @param $users
     */
    public function makeCompetitors($championship, $users)
    {
        foreach ($users as $user) {
            factory(Competitor::class)->create([
                'user_id' => $user->id,
                'championship_id' => $championship->id,
                'confirmed' => 1,]);
        }
    }

    public function generateTreeWithUI($numAreas, $numCompetitors, $preliminaryGroupSize, $hasPlayOff, $hasPreliminary)
    {

        $this->visit('/kendo-tournaments')
            ->select($hasPreliminary, 'hasPreliminary')
            ->select($numAreas, 'fightingAreas')
            ->select($hasPlayOff ? 0 : 1, 'treeType')
            ->select($preliminaryGroupSize, 'preliminaryGroupSize')
            ->select($numCompetitors, 'numFighters');


        $this->press('save');
    }

    /**
     * @param $championship
     * @param $numArea
     * @param $numCompetitors
     * @param $numGroupsExpected
     * @param $currentTest
     */
    protected function checkGroupsNumber($championship, $numArea, $numCompetitors, $numGroupsExpected, $currentTest)
    {
        for ($area = 1; $area <= $numArea; $area++) {
            $count = FightersGroup::where('championship_id', $championship->id)
                ->where('area', $area)
                ->where('round', 1)
                ->count();

            if ((int)($numCompetitors / $numArea) <= 1) {
                $this->assertTrue($count == 0);
            } else {
                $expected = (int)($numGroupsExpected[$numCompetitors - 1] / $numArea);

                if ($count != $expected) {
                    dd(['Method' => $currentTest,
                        'NumCompetitors' => $numCompetitors,
                        'NumArea' => $numArea,
                        'Real' => $count,
                        'Excepted' => $expected,
                        'numGroupsExpected[' . ($numCompetitors - 1) . ']' => $numGroupsExpected[$numCompetitors - 1] . ' / ' . $numArea]);
                }
                $this->assertTrue($count == $expected);
            }
        }
    }

    /**
     * @param $championship
     * @param $numArea
     * @param $numCompetitors
     * @param $numFightsExpected
     * @param $currentTest
     */
    protected function checkFightsNumber($championship, $numArea, $numCompetitors, $numFightsExpected, $currentTest)
    {
        for ($area = 1; $area <= $numArea; $area++) {
            $groupsId = FightersGroup::where('championship_id', $championship->id)
                ->where('area', $area)
                ->where('round', 1)
                ->select('id')
                ->pluck('id')->toArray();

            $count = Fight::whereIn('fighters_group_id', $groupsId)->count();


            if ((int)($numCompetitors / $numArea) <= 1) {
                $this->assertTrue($count == 0);
            } else {
                $log = ceil(log($numFightsExpected[$numCompetitors - 1], 2));

                $expected = pow(2, $log) / $numArea;


                if ($count != $expected) {
                    dd(['Method' => $currentTest,
                        'NumCompetitors' => $numCompetitors,
                        'NumArea' => $numArea,
                        'Real' => $count,
                        'Excepted' => $expected,
                        'numGroupsExpected[' . ($numCompetitors - 1) . ']' => "2 pow " . $log]);
                }
                $this->assertTrue($count == $expected);
            }
        }
    }
}