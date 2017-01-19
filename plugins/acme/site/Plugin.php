<?php namespace Acme\Site;

use Backend;
use Event;
use File;
use System\Classes\PluginBase;
use VojtaSvoboda\Brands\Controllers\Brands;
use VojtaSvoboda\Brands\Models\Brand;
use Yaml;

class Plugin extends PluginBase
{
    /** @var array Plugin dependencies. */
    public $require = [
        'VojtaSvoboda.Brands',
    ];

    public function pluginDetails()
    {
        return [
            'name' => 'Acme Site',
            'description' => 'Acme Site plugin',
            'author' => 'Vojta Svoboda',
            'icon' => 'icon-leaf',
        ];
    }

    public function boot()
    {
        // rename Brands to Clients
        Event::listen('backend.menu.extendItems', function ($manager)
        {
            // override VojtaSvoboda.Brands navigation name
            $manager->addMainMenuItem('VojtaSvoboda.Brands', 'brands', [
                'label' => 'Clients',
            ]);
        });

        // extend VojtaSvoboda.Brand model and add new fields
        Brand::extend(function ($model)
        {
            // add new fillable
            $model->addFillable(['ceo', 'top']);

            // model required fields
            $model->rules['ceo'] = 'min:5';
            $model->rules['top'] = 'boolean';
        });

        // extend VojtaSvoboda.Brand Brands controller
        Brands::extendFormFields(function ($form, $model, $context)
        {
            if (!$model instanceof Brand) {
                return;
            }

            // new fields
            $configFile = __DIR__ . '/config/brands_fields.yaml';
            $config = Yaml::parse(File::get($configFile));
            $form->addFields($config);

            // new tab fields
            $configFile = __DIR__ . '/config/brands_tab_fields.yaml';
            $config = Yaml::parse(File::get($configFile));
            $form->addTabFields($config);
        });

        // extend user listing
        Event::listen('backend.list.extendColumns', function ($widget)
        {
            if (!$widget->getController() instanceof Brands) {
                return;
            }

            if (!$widget->model instanceof Brand) {
                return;
            }

            $widget->addColumns([
                'ceo' => [
                    'label' => 'CEO',
                    'sortable' => true,
                    'searchable' => true,
                ],
                'top' => [
                    'label' => 'Top',
                    'sortable' => true,
                    'type' => 'switch',
                ],
            ]);
        });
    }
}
