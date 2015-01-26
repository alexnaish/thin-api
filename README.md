# PHP Tiny API

Intro 
-----

As I moved away from pure PHP applications and moved to a more modern approach of a JS framework such as Angular, I started needing to provide an API layer for my apps. I really only needed something very lightweight and quick for my apps to tie into and pretty much everything I could have used just contained so much more than I needed. I wanted something that would work on any web hosting environment and be quick and easy to work with.

I started off by defining some conventions. With frameworks like Grails, you get the convention of /Controller/Action/Params. I wanted something similar without the incredible bloat of running a Grails instance. So I've used a combination of the PHP autoloader and a little Apache htaccess magic to define a REST API framework that fits my needs.


Setup
-----

You can place the API anywhere on your hosting. The steps you need to follow to get a quick and easy REST API up and running are:

1. Modify your .htaccess file and change the RewriteBase property to the directory you've placed the code. 
1. Update /lib/config.php to contain your database access credentials.
1. Done.

Usage
-----

All you have is a blank slate. You'll need to define your Controllers & Models before you start seeing any results. Your controllers can do absolutely anything. 

TAPI has the following conventions. For instance:

1. app.get('/sample/?):
 * TAPI will load the SampleController and because of the URL structure it will try to perform the function mapped to "QUERY". If there is no custom mapping defined for QUERY, then TAPI will try to perform the "query" function and if thats not defined in the SampleController class then TAPI will return a 501 Not Implemented and a JSON response indicating the same.
 
1. app.get('/sample/:id/?):
 * As before, TAPI will load the SampleController but because an extra parameter has been defined it will try to perform the function mapped to "GET". If there is no custom mapping defined for GET, then TAPI will try to perform the "get" function and if thats not defined in the SampleController class then TAPI will return a 501 Not Implemented and a JSON response indicating the same. If either of the first two cases exists, TAPI will pass in the parameters as an array into which ever function is called. 
 
1. app.post('/sample/?'):