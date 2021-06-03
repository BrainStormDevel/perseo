# PerSeo beta v2 0.1

This is a beta v2 of PerSeo CMS based on Slim Framework v4

![PerSeo](https://github.com/BrainStormDevel/resources/blob/main/perseo.jpg?raw=true)

This is my CMS (based on Slim Framework 4 using PHP-DI 6 php-di.org), simply, modular, faster, SEO friendly and Secure. Code is PSR-4, PSR-7, PSR-16 compliant, and DB class using Medoo ORM for DB Access. The password are stored with BCRYPT + salt, sensitive data can be encrypted and decrypted with a "salt password" stored in settings.php. There is an Error Handler to log all Errors, silent or not (with a message or simply create a log file).

DB Minimum requirements:
Mariadb 10.0.5+ or MySQL 8.0+

Install and use is really simple.

1) Download or clone this project to your local folder or host.
2) Use composer to install dependencies
3) Go to http://yourhost/ and follow the wizard.

Write your own module is really simple. Just see the "modules" folder content, create a new module, create routes.php file in module folder and start write your own code. To test your code, browse to http://yourhost/yourRouteName and see the result.
