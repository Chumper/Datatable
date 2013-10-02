Datatable
=========

This is a __laravel 4 package__ for the server and client side of datatables at http://datatables.net/

I developed this package because i was not happy with the only existing package at https://github.com/bllim/laravel4-datatables-package
so i developed this package with in my opinion is superior.

![Image](https://raw.github.com/Chumper/Datatable/master/datatable.jpg)

##Please note

At the moment i only finished the collection part, so this package will work with collections but not with queries.
I am working on that, so this is a package under development and you use it at your **own** risk. 

Please let me know any issues or features you want to have in the issues section.
I would be really thankful if you can provide a test that points to the issue.

##Known Issues

* If you supply `created_at` or `updated_at` in `showColumns` it will return the object, not the string.

##TODO

* add a method to search only in the given columns
* same for the order ability

##Features

This package supports:

*   Support for Collections and Query strings (only Collections atm, not finished and tested for queries)
*   Easy to add and order columns
*   Includes a simple helper for the HTML side
*   Use your own functions and presenters in your columns
*   Search in your custom defined columns ( Collection only!!! )
*   Tested! (Ok, maybe not fully, but I did my best :) )

##Installation

This package is available on http://packagist.org, just add it to your composer.json
	
	"chumper/datatable": "dev-master"

It also has a ServiceProvider for usage in Laravel4. Add these lines to app.php:

```php
    // providers array:	
	'Chumper\Datatable\DatatableServiceProvider',

    // aliases array:
    'Datatable' => 'Chumper\Datatable\Facades\Datatable',
```

You can then access it under the `Datatable` alias.


##Basic Usage

* Create two routes: One to deliver the view to the user, the other for datatable data, eg:

```php
    Route::resource('users', 'UsersController');
    Route::get('api/users', array('as'=>'api.users', 'uses'=>'UsersController@getDatatable'));
```

* Your main route will deliver a view to the user. This view should include references to your local copy of [datatables](http://datatables.net/). In the example below, files were copied from the datatables/media directories and written to public/assets. Please note that the scripts must be located above the call to Datatable:

```php
    <link rel="stylesheet" type="text/css" href="/assets/css/jquery.dataTables.css">
    <script type="text/javascript" src="/assets/js/jquery.js"></script>
    <script type="text/javascript" src="/assets/js/jquery.dataTables.min.js"></script>

    {{ DataTable::table()
    ->addColumn('id','Name')       // these are the column headings to be shown  
    ->setUrl(route('api.users'))   // this is the route where data will be retrieved
    ->render() }}
```

* Create a controller function to return your data in a way that can be read by Datatables:

```php
    public function getDatatable()
    {
        return Datatable::collection(User::all(array('id','name')))
        ->showColumns('id', 'name')
        ->make();
    }
```

You should now have a working datatable on your page.


##HTML Example

```php
	Datatable::table()
    ->addColumn('id',Lang::get('user.lastname'))
	->setUrl(URL::to('auth/users/table'))
    ->render();
```

This will generate a HTML table with two columns (id, lastname -> your translation) and will set the URL for the ajax request.

>   Note: This package will **NOT** include the `datatable.js`, that is your work to do.
>   The reason is that for example i use Basset and everybody wants to do it their way...

##Server Example

```php
	Datatable::collection(User::all())
    ->showColumns('id')
    ->addColumn('name',function($model)
        {
            return $model->getPresenter()->yourProperty
        }
    )->make();
```

This will generate a server side datatable handler from the collection `User::all()`.
It will add the `id` column to the result and also a custom column called `name`.
Please note that we need to pass a function as a second parameter, it will **always** be called
with the object the collection holds. In this case it would be the `User` model.

You could now also access all relationship, so it would be easy for a book model to show the author relationship.

```php
	Datatable::collection(User::all())
    ->showColumns('id')
    ->addColumn('name',function($model)
        {
            return $model->author->name;
        }
    )->make();
```

>   Note: If you pass a collection of arrays to the `collection` method you will have an array in the function, not a model.

The order of the columns is always defined by the user and will be the same order the user adds the columns to the Datatable.

##Query or Collection?

There is a difference between query() and relationship().
A collection will be compiled before any operation like search or order will be performed so that it can also include your custom fields.
This said the collection method is not as performing as the query method where the search and order will be tackled before we query the database.

So if you have a lot of Entries (100k+) a collection will not perform well because we need to compile the whole amount of entries to provide accurate sets.
A query on the other side is not able to perform a search or orderBy correctly on your custom field functions.

>   TLTR: If you have no custom fields, then use query() it will be much faster
>   Collection is the choice if you have data from somewhere else, just wrap it into a collection and you are good to go.
>   If you have custom fields and want to provide search and/or order on these, you need to use a collection.

##Available function

This package is seperated into three smaller ones:

1.  Datatable::collection()
2.  Datatable::query()
3.  Datatable::table()

The first two are for the server side, the third one is a helper to generate the needed table and javascript call.

###Collection & Query

**collection($collection)**

Will set the internal engine to the collection.
For further performance improvement you can limit the number of columns and rows, i.e.:

	$users = User::activeOnly()->get('id','name');
	Datatable::collection($users)->...

**query($query)**

This will set the internal engine to a Eloquent query...
This is not finished yet, so please be patient.

**showColumns(...$columns)**

This will add the named columns to the result.
>   Note: You need to pass the name in the format you would access it on the model or array.
>   example: in the db: `last_name`, on the model `lastname` -> showColumns('lastname')

You can provide as many names as you like

**addColumn($name, $function)**

Will add a custom field to the result set, in the function you will get the whole model or array for that row

**make()**

This will handle the input data of the request and provides the result set.
> Without this command no response will be returned.

**clearColumns()**

This will reset all columns, mainly used for testing and debugging, not really useful for you.
>   If you don't provide any column with `showColumn` or `addColumn` then no column will be shown.
>   The columns in the query or collection do not have any influence which column will be shown.

**getOrder()**

This will return an array with the columns that will be shown, mainly used for testing and debugging, not really useful for you.

**getColumn($name)**

Will get a column by its name, mainly used for testing and debugging, not really useful for you.

###Table

**setUrl($url)**

Will set the URL and options for fetching the content via ajax.

**setOptions($name, $value) OR setOptions($array)**

Will set a single option or an array of options for the jquery call.

**addColumn($name)**

Will add a column to the table, where the name will be rendered on the table head.
So you can provide the string that should be shown.

**countColumns()**

This will return the number of columns that will be rendered later. Mainly for testing and debugging.

**getData()**

Will return the data that will be rendered into the table as an array.

**getOptions()**

Get all options as an array.

**render()**

Renders the table

**setData($data)**

Expects an array of arrays and will render this data when the table is shown.

##License

This package is licensed under the MIT License
