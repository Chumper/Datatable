Datatable
=========

This is a laravel 4 package for the server and client side of datatables at http://datatables.net/

I developed this package because i was not happy with the only existing package at https://github.com/bllim/laravel4-datatables-package
so i developed this package with in my opinion is superior.

##Known Issues

* If you supply `created_at` or `updated_at` in `showColumns` it will return the object, not the string.

##Features

This package supports:

*   Support for Collections and Query strings (only Collections atm, not finished and tested for queries)
*   Easy to add and order columns
*   Includes a simple helper for the HTML side
*   Use your own functions and presenters in your columns
*   Search in your custom defined columns ( Collection only!!! )

##HTML Example

	Datatable::table()
    ->addColumn('id',Lang::get('user.lastname'))
	->setUrl(URL::to('auth/users/table'))
    ->render(),

This will generate a HTML table with two columns (id, lastname -> your translation) and will set the URL for the ajax request.

>   Note: This package will **NOT** include the `datatable.js`, that is your work to do.
>   The reason is that for example i use Basset and everybody wants to to it their way...

##Server Example
	Datatable::collection(User::all())
    ->showColumns('id')
    ->addColumn('name',function($model)
        {
            return $model->getPresenter()->
        }
    )->make();

This will generate a server side datatable handler from the collection `User::all()`.
It will add the `id` column to the result and also a custom column called `name`.
Please note that we need to pass a function as a second parameter, it will **always** be called
with the object the collection holds. In this case it would be the `User` model.

>   Note: If you pass a collection of arrays to the `collection` method you will have an array in the function, not a model.

##Available function

This package is seperated into three smaller ones:

1.  Datatable::collection()
2.  Datatable::query()
3.  Datatable::table()

The first two are for the server side, the third one is a helper to generate the needed table and javascript call.


This package is not production ready or able to be used, as soon as i finish developing i will let you know
===
