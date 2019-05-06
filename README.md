# PerSeo beta 0.9

This is my CMS (based on Slim Framework 3), simply, modular, faster, SEO friendly and Secure. Code is PSR-4, PSR-7, PSR-16 compliant, and DB class using Medoo ORM for DB Access. The password are stored with SHA512 + salt, sensitive data can be encrypted and decrypted with a "salt password" stored in config.php. Request are sanitized using Methods $container->Sanitizer->GET and $container->Sanitizer->POST to prevent XSS, CSRF.


Install and use is really simple.

1) Download or clone this project to your local folder or host.
2) Use composer to install dependencies
3) Go to http://yourhost/ and follow the wizard.

Write your own module is really simple. Just see the "modules" folder content, create a new module, create routes.php file in module folder and start write your own code. To test your code, browse to http://yourhost/yourRouteName and see the result.
