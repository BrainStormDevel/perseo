# PerSeo beta 0.1

This is my CMS, simply, modular, faster, SEO friendly and Secure. The password are stored with SHA512 + salt, sensitive data can be encrypted and decrypted with a "password" stored in config.php. Special vars Request::GET and Request::POST are sanitized to prevent SQL Injection, XSS, CSRF.


Install and use is really simple.

1) Download or clone this project to your local folder or host.
2) Use composer to install dependencies
3) Go to http://yourhost/wizard and follow the wizard.

Write your own module is really simple. Just see the "modules" folder content, create a new module and start write your own code. To test your code, browse to http://yourhost/yourmodulename and see the result.
