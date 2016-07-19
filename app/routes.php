<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/


//automated routing to each application controller

$seg = array();
for($i=1;$i<=3;$i++) {
	$seg[] = Request::segment($i);
}
if(!empty($seg[0])) {
	//set default route
	if(empty($seg[1])) $seg[1] = Config::get('app.default_controller');
	if(empty($seg[2])) $seg[2] = Config::get('app.default_action');
	
	//views namespace for each modules
	//View::addLocation(app('path').'/views/'.$seg[0].'/'.$seg[1]);
	View::addNamespace($seg[1], app('path').'/views/'.$seg[0].'/'.$seg[1]);
	
	//view namespace for general purpose
	//View::addLocation(app('path').'/views/'.$seg[0].'/General');
	View::addNamespace('General', app('path').'/views/'.$seg[0].'/General');
	
	//Handling 404 Errors for each application
	App::missing(function($exception) {
		//return Response::view('General::error.404', array(), 404);
		return 'Not Found';
	});
	
	//here are the tricks ;-)
	ClassLoader::addDirectories(array(
		app_path().'/controllers/'.$seg[0],
		app_path().'/models/'.$seg[0]
	));
	Route::any(
		$seg[0].'/'.
		$seg[1].'/'.
		$seg[2].'/'.
		'{seg1?}/{seg2?}/{seg3?}/{seg4?}/{seg5?}/{seg6?}/{seg7?}',
		array(
			'uses'=>"{$seg[1]}Controller@{$seg[2]}"
		)
	);
	//end of the tricks
}

//prevent not found exception on root
Route::any('/', function(){
	return 'Nothing here';
});

//handle error application not found

App::error(function(ReflectionException $exception)
{
	Log::error($exception);
    return 'Application not found';
});


//run default route
Route::any($seg[0], "{$seg[1]}Controller@{$seg[2]}");

//goodbye $seg, your job is done
unset($seg);


/* query logging */

if (Config::get('database.log', false))
{           
    Event::listen('illuminate.query', function($query, $bindings, $time, $name)
    {
        $data = compact('bindings', 'time', 'name');

        // Format binding data for sql insertion
        foreach ($bindings as $i => $binding)
        {   
            if ($binding instanceof \DateTime)
            {   
                $bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
            }
            else if (is_string($binding))
            {
                $bindings[$i] = "'$binding'";
            }
        }

        // Insert bindings into query
        $query = str_replace(array('%', '?'), array('%%', '%s'), $query);
        $query = vsprintf($query, $bindings); 

        Log::info($query, $data);
    });
}
	
	
	
	

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	