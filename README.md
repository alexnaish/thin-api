# PHP Tiny API

Intro 
-----

As I moved away from pure PHP applications and moved to a more modern approach of a JS framework such as Angular, I started needing to provide an API layer for my apps. I really only needed something very lightweight and quick for my apps to tie into and pretty much everything I could have used just contained so much more than I needed. I wanted something that would work on any web hosting environment and be quick and easy to work with.

I started off by defining some conventions. With frameworks like Grails, you get the convention of /Controller/Action/Params. I wanted something similar without the incredible bloat of running a Grails instance. So I've used a combination of the PHP autoloader and a little Apache htaccess magic to define a REST API framework that fits my needs.


Examples
-----

With this code, you can place the API anywhere on your hosting. The steps you need to follow to get a quick and easy REST API up and running are:

1. Modify your .htaccess file and change the RewriteBase property to the directory you've placed the code. 
1. Update /lib/config.php to contain your database access credentials.
1. Done.

Now at this point, all you have is a blank slate. You'll need to define your Controllers & Models before you start seeing any results.

TAPI has the following conventions. For instance:

1. GET of $BASE_URL/sample:
 * TAPI will load the SampleController and because of the URL structure it will try to perform the function mapped to "QUERY". If there is no custom mapping defined, then TAPI will try to perform the query function and if thats not defined in the SampleController class then TAPI will give up all hope and return a 501 Not Implemented and a JSON response indicating the same.