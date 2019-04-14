# Brands plugin override examples

In this repository you can find examples how to extend OctoberCMS backend, or OctoberCMS plugins. This text covers 
this cases:

- add new menu item
- update menu item
- remove menu item
- extend model class
- add new field to backend form
- remove item from backend form
- update backend form item
- add new columns to backend list

## Real example

There is also Plugin.php file where you can find real example how to take 
[Brands plugin](http://octobercms.com/plugin/vojtasvoboda-brands) and use it for managing your Clients.

We want to use Brands plugin to manage our Clients. So we have to rename backend main menu item from Brands to Clients. 
Then we need to add new fields `ceo` (Company CEO name) and `top` (if this company is our TOP client, or not).

When extending some plugin, first step should be **create new plugin** and add all extending method here. Never change 
plugin functionality directly at the plugin's folder, because you lose all changes after first update.

So we create new plugin Acme.Site by this terminal command in our project root folder:

```
php artisan create:plugin Acme.Site
```

Then open newly generate Plugin.php file and copy-paste **extending blocks** from examples below.

1. For rename main menu item see section **Rename main menu item**.
2. Then we need to add new fields `ceo` and `top` to the database, because we need this informations to be saved. 
We have to create migration file (see updates folder in this plugin's directory). And then add this migration 
at `version.yaml` file. After we have created migration we have to run `php artisan october:up` to run migrations.
3. When the database is updated we can add this new fields to the Model to make them visible and to add 
some validations rules. For add them to the Model see section **Extend model class**.
4. New fields are ready now, so we need them at the backend form, to fill them. For that see section **Add new form fields**.
5. Finally we want to show them at the backend listing. For extend backend listing see **Add new columns to backend list**.

## Add new menu item

Add these lines to Plugin.php to the boot() method:

```
Event::listen('backend.menu.extendItems', function ($manager)
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

For adding side menu items use `$manager->addSideMenuItem()` method.

## Update menu item

Add these lines to Plugin.php to the boot() method:

```
Event::listen('backend.menu.extendItems', function ($manager)
{
    // override VojtaSvoboda.Brands navigation name
    $manager->addMainMenuItem('VojtaSvoboda.Brands', 'brands', [
        'label' => 'Clients',
    ]);
});
```

You have to open plugin's Plugin.php file and at method `registerNavigation()` find menu group name, e.g. brands. 
And then rewrite only fileds you want to override (at this example only `label` field).

For adding side menu items use `$manager->addSideMenuItem()` method.

## Remove menu item

For remove menu items use `$manager->removeMainMenuItem()` and `$manager->removeSideMenuItem()` method.

```
$manager->removeMainMenuItem('VojtaSvoboda.Brands', 'brands');
$manager->removeSideMenuItem('VojtaSvoboda.Brands', 'brands', 'items');
```

## Extend model class

When you adding new fields to model, be sure you have created migration. For example see 
file `plugins/acme/site/updates/create_brands_attributes.php`. This migration has to be mentioned at `version.yaml`.

After create migration, you have to run it by `php artisan october:up` command.

```
// extend VojtaSvoboda.Brand model
Brand::extend(function ($model)
{
    // add new fillable fields
    $model->addFillable(['ceo', 'top']);

    // add model required fields
    $model->rules['ceo'] = 'min:5';
    $model->rules['top'] = 'boolean';
});
```

## Add new form fields

```
// extend VojtaSvoboda.Brand Brands controller
Brands::extendFormFields(function ($form, $model, $context)
{
    // apply only for Brand model
    if (!$model instanceof Brand) {
        return;
    }

    // add new fields
    $configFile = __DIR__ . '/config/brands_fields.yaml';
    $config = Yaml::parse(File::get($configFile));
    $form->addFields($config);
});
```

For adding new tab fields use `$form->addTabFields()` or `$form->addSecondaryTabFields()` method.

## Remove form fields

Code is the same like for Add new form field above, just change method name:

```
// remove logo
$form->removeField('logo');
```

## Update form fields

Just remove form item and than add it again with new parameters.

## Add new columns to backend list

```
// extend user listing
Event::listen('backend.list.extendColumns', function ($widget)
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

## Override translations

In lang folder you can find two examples of overriding translations - for core module and plugin.
