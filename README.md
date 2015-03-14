# PHP Tiny API

[![Build Status](https://travis-ci.org/alexnaish/thin-api.svg)](https://travis-ci.org/alexnaish/thin-api)

Intro 
-----

As I moved away from pure PHP applications and moved to a more modern approach of a JS framework such as Angular, I started needing to provide an API layer for my apps. I really only needed something very lightweight and quick for my apps to tie into and pretty much everything I could have used just contained so much more than I needed. I wanted something that would work on any web hosting environment and be quick and easy to work with.

I started off by defining some conventions. With frameworks like Grails, you get the convention of /Controller/Action/Params. I wanted something similar without the incredible bloat of running a Grails instance. So I've used a combination of the PHP autoloader and a little Apache htaccess magic to define a REST API framework that fits my needs.

Setup
-----

You can place the API anywhere on your hosting. The steps you need to follow to get a quick and easy REST API up and running are:

1. Clone the Repository and copy everything in the src folder into the directory you want your API to exist on.
1. Modify the .htaccess file and change the RewriteBase property to the directory you've placed the code. 
1. Update /lib/config.php to contain your database access credentials (if required).
1. Remove the test directory as required.
1. Done.

Testing
-----

To run the tests please download PHPUnit as instructed [here](https://phpunit.de/manual/current/en/installation.html#installation.phar). Once you have downloaded and installed it, just run PHPUnit in the root folder of the project. Test are ran on Travis CI.

Usage
-----

All you have is a blank slate. You'll need to define your Controllers & Models before you start seeing any results. Your controllers can do absolutely anything. 

TAPI has the following conventions. For instance:

1. The first parameter passed into the URL will map to your controller. For instance "http://$API_BASE_DIR/test/" will load the TestController class. If we attempt to access "http://$API_BASE_DIR/example/blah" will load the ExampleController class. All controller classes must be suffixed with "Controller" and be found in the "controllers" directory.
2. All subsequent URL parameters after the first will be passed into your controller functions as an indexed array.
3. POST and PUT requests require the data payload to be in the JSON format. TAPI will decode the JSON and convert it into an associative array. Once converted TAPI will pass the payload into your respective POST/PUT function as the second parameter.

Routing
-----

1. app.get('$BASE/sample/?'):
 * TAPI will load the SampleController and because of the URL structure it will try to perform the function mapped to "QUERY". If there is no custom mapping defined for QUERY, then TAPI will try to perform the "query" function and if thats not defined in the SampleController class then TAPI will return a 501 Not Implemented and a JSON response indicating the same.
 
1. app.get('$BASE/sample/:id/*'):
 * As before, TAPI will load the SampleController but because an extra parameter has been defined it will try to perform the function mapped to "GET". If there is no custom mapping defined for GET, then TAPI will try to perform the "get" function and if thats not defined in the SampleController class then TAPI will return a 501 Not Implemented and a JSON response indicating the same. If either of the first two cases exists, TAPI will pass in the parameters as an array into which ever function is called. 
 
1. app.post('$BASE/sample/:optional*'):
 * TAPI will load the SampleController class and will look for a custom mapping against 'POST'. If it is set it will execute the mapped function otherwise it will attempt to execute the "save" function. As before, if neither condition is met TAPI will return the standard not implented response. If a condition is met TAPI will pass both the POST payload and the URL parameters to the function with the URL parameters passed in as an indexed array and the payload as an associative array. 
 
1. app.put('$BASE/sample/:optional*'):
 * TAPI will load the SampleController class and will look for a custom mapping against 'PUT'. If set, then it will parse the function and attempt to run it as a method defined within the class. If no mapping is set TAPI will fall back to the default method and execute the "update" function. If this is not defined within the class extending the API class then it will fall back to the base class method and will return the standard not implented response.
 
1. app.del('$BASE/sample/:optional*'):
 * TAPI will load the SampleController class and will look for a custom mapping against 'DELETE'. If there is no mapping then TAPI will attempt to execute the "delete" method. If there is no delete method on the Controller then TAPI will use the default "delete" method and provide the standard not implemented response.
 
 Questions
 -----
 
 Feel free to contact me at alex.naish.90@gmail.com. This project is free for anyone to modify and use but I would love if you would let me know if you do!
