# VPPM

![Laravel Logo](https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg)

## About the Project

**A.L.M.A.N.A.C** - VPPM is a web application designed to tracks project progress percentage. It aims to provide users with a seamless experience in Monitoring, Logging, and Analize project progress.

## Features

- **Feature 1**: - -
- **Feature 2**: - -
- **Feature 3**: - -

## Installation Requirements

To set up the project locally, ensure you have the following prerequisites:

- PHP 8.2
- Composer


# Error Handling

-- **Error -> Forbidden**: --

```
If you encounter a "Forbidden" error, you may need to change the PHP version. Follow these steps:

Go to the PHP Selector.
Navigate to PHP Manager.
Select the appropriate PHP version.
```

-- **Error -> ErrorException**: --

```
Solutions StepByStep:
	1. mv /app/services /app/Services
	2. run these:
		composer dump-autoload
		php artisan config:cache
		php artisan cache:clear
		php artisan view:clear
```

