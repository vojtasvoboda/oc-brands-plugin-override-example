# Brands plugin override examples

This plugin shows how to extend and override backend or some plugin functionality. All examples is applied 
to [Brands plugin](http://octobercms.com/plugin/vojtasvoboda-brands) to having real use cases.

For all overrides is best approach to create new MyWebsite.Site and then continue with use cases below.

```
php artisan create:plugin MyWebsite.Site
```

At Plugin.php in this repository, there is example of how to add new fields to Brands plugin.

## Add new main menu

Add these lines to Plugin.php to the boot() method:

```
Event::listen('backend.menu.extendItems', function($manager)
{
    $manager->addMainMenuItem('MyWebsite.Site', 'items', [
        'label' => 'Items',
        'url' => Backend::url('mywebsite/site/items'),
        'icon' => 'icon-leaf',
        'permissions' => ['mywebsite.site.items'],
        'order' => 500,
    ]);
});
```

## Override plugin's main menu item

Add these lines to Plugin.php to the boot() method:

```
Event::listen('backend.menu.extendItems', function($manager)
{
    // override VojtaSvoboda.Brands navigation name
    $manager->addMainMenuItem('VojtaSvoboda.Brands', 'brands', [
        'label' => 'Clients',
    ]);
});
```

You have to open plugin's Plugin.php file and at method `registerNavigation()` find menu group name, e.g. brands. 
And then rewrite only fileds you want to override (at this example only `label` field).

## Extend model class

```
// extend VojtaSvoboda.Brand model
Brand::extend(function($model)
{
    // add new fillable fields
    $model->addFillable(['ceo', 'top']);

    // add model required fields
    $model->rules['ceo'] = 'min:5';
    $model->rules['top'] = 'boolean';
});
```

## Extend form fields

```
// extend VojtaSvoboda.Brand Brands controller
Brands::extendFormFields(function($form, $model, $context)
{
    if (!$model instanceof Brand) {
        return;
    }

    // new fields
    $configFile = __DIR__ . '/config/brands_fields.yaml';
    $config = Yaml::parse(File::get($configFile));
    $form->addFields($config);
});
```

## Removing form items

```
// remove logo
$form->removeField('logo');
```

## Move form item to different place

Just remove form item and than add it again to new place.

## Extend listing columns

```
// extend user listing
Event::listen('backend.list.extendColumns', function($widget)
{
    // only for Brands controller
    if (!$widget->getController() instanceof Brands) {
        return;
    }

    // only for Brand model
    if (!$widget->model instanceof Brand) {
        return;
    }

    // add new column
    $widget->addColumns([
        'ceo' => [
            'label' => 'CEO',
            'sortable' => true,
            'searchable' => true,
        ],
    ]);
});
```
