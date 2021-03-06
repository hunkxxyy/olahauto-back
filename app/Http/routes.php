<?php



Route::get('/', function () {
    return view('welcome');
});

/*todo: Megjegyzés az oauth2vel kapcsolatban
a client secret ssh titkosított ez okoz problémát. Figyelj rá*/


/*Navigation menu*/
/*-------------------------------- Dragable --------------------------------------*/

Route::get('api/dragable-menu/all','olahauto\DragableMenuController@all');

Route::get('api/dragable-menu/{id}','olahauto\DragableMenuController@show');

Route::put('api/dragable-menu/move','olahauto\DragableMenuController@move');
Route::get('api/dragable-menus/{query}','olahauto\DragableMenuController@listWithFilters');
Route::post('api/dragable-menu/','olahauto\DragableMenuController@store');
Route::put('api/dragable-menu/modify/{id}','olahauto\DragableMenuController@update');
Route::put('api/dragable-menu/archive/{id}','olahauto\DragableMenuController@archive');


/*-------------------------Ideiglene adatbázis átmásolási funkciók-------------------------*/
Route::get('db/seed','olahauto\PullDatasFromOldDatabaseController@seed');
/*------------------------------------Dynamic Html Pages------------------------------------*/
Route::get('api/getcontents/','DynamicHtmlControllers@getAll');
Route::put('api/setcontent','DynamicHtmlControllers@update');
Route::put('api/getcontent','DynamicHtmlControllers@getContent');

/*-----------------------------------------CKEDITOR-----------------------------------------*/
Route::put('api/ckeditor/images/list','CkeditorController@listOfImages');
Route::post('api/ckeditor/fileupload/','CkeditorController@store');
Route::put('api/ckeditor/filedelete/{id}','CkeditorController@filedelete');
Route::put('api/ckeditor/update/{id}','CkeditorController@update');
/*-----------------------------------------SEARCH-----------------------------------------*/
Route::get('api/search/{word}','SearchController@search');

/*----------------------------------Authentication OAUTH2----------------------------------*/
Route::post('auth/login','Auth\Oauth2Controller@loginPost');
Route::get('auth/exit/','Auth\Oauth2Controller@destroy');
Route::get('oauth/usertype/','Auth\Oauth2Controller@usertype');
Route::get('oauth/checkvalid/{token}','Auth\Oauth2Controller@checkValid');






//ingatlanlotto
Route::get('Szintek/menu','SzintekController@menu');
Route::get('Szintek/menulogged','SzintekController@menuLogged');
//Route::get('Szintek/menu/{token}','SzintekController@menu');
Route::get('Szintek/tartalom/all','SzintekController@showall');
Route::get('Szintek/tartalom/{szint_id}','SzintekController@tartalom');
Route::post('Szintek/tartalom/{szint_id}','SzintekController@update');
Route::get('/register',function(){$user = new App\User();
    $user->name="hunk";
    $user->email="hunk74@gmail.com";
    $user->password = \Illuminate\Support\Facades\Hash::make("Editke76");
    $user->save();
});

Route::get('register/admin',function(){$user = new App\User();
    $user->name="admin";
    $user->email="admin@gmail.com";
    $user->password = \Illuminate\Support\Facades\Hash::make("jelszó");
    $user->save();
});




Route::get('git/pull','GitController@pull');

/*-------------------------------- USER --------------------------------------*/
Route::get('api/users/{query}','UserController@listWithFilters');
Route::get('api/user/{id}','UserController@show');
Route::get('api/currentuser/','UserController@showMyData');
Route::post('api/user/','UserController@store');
Route::put('api/user/modify/{id}','UserController@update');
Route::put('api/currentuser/modify/','UserController@updateCurrent');
Route::get('api/user/passwordreminder/{email}','UserController@passwordreminder_send');
Route::put('api/user/forgottenpasswordrchange','UserController@forgottenpasswordrchange');

Route::put('api/user/archive/{id}','UserController@archive');


/*-------------------------------- INGATLADB --------------------------------------*/
Route::get('api/ingatlans/kivalasztott','IngatlanController@kivalasztott');
Route::get('api/ingatlans/{query}','IngatlanController@listWithFilters');
Route::get('api/ingatlan/{id}','IngatlanController@show');
Route::get('api/ingatlan/{id}/licits','IngatlanController@showlicits');
Route::get('api/ingatlan/licit/toplista/{id}','LicitController@showlicitToplista');
Route::get('api/ingatlan/licit/fuggobenleve/{id}','LicitController@fuggobenleve');
Route::post('api/ingatlan/','IngatlanController@store');
Route::put('api/ingatlan/modify/{id}','IngatlanController@update');
Route::put('api/ingatlan/archive/{id}','IngatlanController@archive');


/*-------------------------------- INGATLANIngatlanKepek --------------------------------------*/

Route::get('api/ingatlan_kepeks/{query}','IngatlanKepekController@listWithFilters');
Route::get('api/ingatlan_kepek/dir/','IngatlanKepekController@makedir');
Route::get('api/ingatlan_kepek/{id}','IngatlanKepekController@show');
Route::post('api/ingatlan_kepek/','IngatlanKepekController@store');
Route::put('api/ingatlan_kepek/modify/{id}','IngatlanKepekController@update');
Route::put('api/ingatlan_kepek/archive/{id}','IngatlanKepekController@archive');

/*-------------------------------- CheckVisiting --------------------------------------*/

Route::get('api/visited/','VisitedController@show');
Route::post('api/visited/','VisitedController@store');
/*-------------------------------- LICIT --------------------------------------*/

Route::get('api/licits/all/{filter}','LicitController@all');
Route::get('api/licits/{query}','LicitController@listWithFilters');
Route::put('api/licits/fizetve/{id}','LicitController@fizetve');

Route::get('api/licit/{id}','LicitController@show');
Route::post('api/licit/','LicitController@store');
Route::put('api/licit/modify/{id}','LicitController@update');
Route::put('api/licit/archive/{id}','LicitController@archive');
/*-------------------------------- NEWS --------------------------------------*/

Route::get('api/news/{query}','NewController@listWithFilters');
Route::get('api/new/{id}','NewController@show');
Route::post('api/new/','NewController@store');
Route::put('api/new/modify/{id}','NewController@update');
Route::put('api/new/archive/{id}','NewController@archive');
/*-------------------------------- Eredmenyek --------------------------------------*/

Route::get('api/eredmenyeks/{query}','EredmenyekController@listWithFilters');
Route::get('api/eredmenyek/{id}','EredmenyekController@show');
Route::post('api/eredmenyek/','EredmenyekController@store');
Route::put('api/eredmenyek/modify/{id}','EredmenyekController@update');
Route::put('api/eredmenyek/archive/{id}','EredmenyekController@archive');
/*-------------------------------- Kapcsolat --------------------------------------*/

Route::get('api/kapcsolat/','KapcsolatController@getParams');
Route::post('api/kapcsolat/','KapcsolatController@setParams');

Route::post('api/kapcsolat/email','KapcsolatEmailController@sendemail');

//TesztMail
Route::get('tesztemail','LicitController@sendTesztEmail');

