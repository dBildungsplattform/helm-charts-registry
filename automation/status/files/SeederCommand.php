<?php

/*
 * This file is part of Cachet.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that is next to this file.
 */

namespace CachetHQ\Cachet\Console\Commands;
use AltThree\Validator\ValidationException;
use CachetHQ\Cachet\Models\Action;
use CachetHQ\Cachet\Models\Component;
use CachetHQ\Cachet\Models\ComponentGroup;
use CachetHQ\Cachet\Models\Incident;
use CachetHQ\Cachet\Models\IncidentTemplate;
use CachetHQ\Cachet\Models\IncidentUpdate;
use CachetHQ\Cachet\Models\Metric;
use CachetHQ\Cachet\Models\MetricPoint;
use CachetHQ\Cachet\Models\Schedule;
use CachetHQ\Cachet\Models\Subscriber;
use CachetHQ\Cachet\Models\User;
use CachetHQ\Cachet\Settings\Repository;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

/**
 */
class SeederCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'cachet:seeder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set ups Cachet';

    /**
     * The settings repository.
     *
     * @var \CachetHQ\Cachet\Settings\Repository
     */
    protected $settings;

    protected $seedData;
    /**
     * Create a new demo seeder command instance.
     *
     * @param \CachetHQ\Cachet\Settings\Repository $settings
     *
     * @return void
     */
    public function __construct(Repository $settings)
    {
        parent::__construct();
        $this->settings = $settings;
        $this->componentGroups = [];
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $seed_data_path = __DIR__ . '/../../seedData.json';
        if(is_file($seed_data_path)) {
            $this->info('Seed data found');
            $this->seedData = json_decode(file_get_contents($seed_data_path), true);
        } else{
            $this->error('Seed data not found');
        }
        $this->info('Starting setup of Cachet');
        $this->seedUsers();
        $this->seedIncidentTemplates();
        $this->seedComponentGroups();
        $this->seedComponents();
        $this->seedSettings();
        $this->info('Setup ran successfully!');
    }


    /**
     * Seed the component groups table.
     *
     * @return void
     */
    protected function seedIncidentTemplates()
    {
        if(isset($this->seedData["enableSeedData"]) && $this->seedData["enableSeedData"]=="true"){
            $this->info('Updating all incident templates');
            if(isset($this->seedData["incidentTemplates"])){
                foreach ($this->seedData["incidentTemplates"] as $template) {
                    IncidentTemplate::updateOrCreate(['name' => $template['name']],
                                                   [
                                                       'name'      => $template['name'],
                                                       'template'  => $template['template']
                                                   ]);
                }
            }
        }
    }

    /**
     * Seed the component groups table.
     *
     * @return void
     */
    protected function seedComponentGroups()
    {
        if(isset($this->seedData["enableSeedData"]) && $this->seedData["enableSeedData"]=="true"){
            $this->info('Updating all component groups');
            if(isset($this->seedData["componentGroups"])){
                $index=1;
                foreach ($this->seedData["componentGroups"] as $group) {
                    ComponentGroup::updateOrCreate(['name' => $group['name']],
                                                   [
                                                       'name'      => $group['name'],
                                                       'order'     => $index,
                                                       'collapsed' => 0,
                                                       'visible'   => ComponentGroup::VISIBLE_GUEST,
                                                   ]);
                    $this->componentGroups[$group['name']] = $index;
                    $index++;
                }
            }
        }
    }

    /**
     * Seed the components table.
     *
     * @return void
     */
    protected function seedComponents()
    {
        if(isset($this->seedData["enableSeedData"]) && $this->seedData["enableSeedData"]=="true"){
            $this->info('Updating all components');
            if(isset($this->seedData["components"])){
                foreach ($this->seedData["components"] as $component) {
                    Component::updateOrCreate(['name' => $component['name']],
                                                   [
                                                       'name'             => $component['name'],
                                                       'description'      => $component['description'],
                                                       'status'           => 1,
                                                       'order'            => 0,
                                                       'group_id'         => $this->componentGroups[$component["componentGroup"]],
                                                       'link'             => $component['link']
                                              ]);
                }
            }
        }
    }


    /**
     * Seed the settings table.
     *
     * @return void
     */
    protected function seedSettings()
    {       
        $this->info('Updating all settings');
        if(isset($this->seedData["settings"])){
            foreach ($this->seedData["settings"] as $setting_key => $setting_value) {
                $this->settings->set($setting_key, $setting_value);
            }
        }
    }


    /**
     * Seed the users table.
     *
     * @return void
     */
    protected function seedUsers()
    {
        if(isset($this->seedData["enableSeedData"]) && $this->seedData["enableSeedData"]=="true"){
            $this->info('Updating all users');
            if(isset($this->seedData["users"])){
                foreach ($this->seedData["users"] as $user) {
                    $level= User::LEVEL_USER;
                    if(isset($user['level']) && $user['level']=='admin'){
                        $level= User::LEVEL_ADMIN;
                    }
                    $password = env($user['password_env'], Str::random(20));
                    $api_key = env($user['api_key_env'], Str::random(20));
                    try{
                        User::updateOrCreate(['username' => $user['username']],
                        [
                            'username' => $user['username'],
                            'email' => $user['email'],
                            'level' => $level,
                            'password' => $password,
                            'api_key' => $api_key
                        ]);
                    } catch(ValidationException $e){
                        $this->error('Validation error: ' . $e->getMessage());
                        $this->error('User data: '. $user);
                    }
                }
            }
        }
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }
}