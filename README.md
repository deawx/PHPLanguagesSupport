# PHPLanguagesSupport
Helper library for adding multilingual support to your project.

## Installation

```
composer require muhametsafak/phplanguagessupport
```

This is a library consisting of a single file and a single class. You can choose to manually include the `src/Language.php` file in the project. However, I recommend Composer to manage patches and updates more easily.

## Usage

Directory structure:

```
App/Languages/
    en/
        Main.php
        Admin.php
    tr/
        Main.php
        Admin.php
```

`en/Admin.php` :

```php
<?php
return [
    'dashboard' => 'Admin Dashboard',
    'hello'     => 'Hello World',
    'day'       => 'Today {date}'
];
```

`tr/Admin.php` :

```php
<?php
return [
    'dashboard' => 'Yönetim Paneli',
    'hello'     => 'Merhaba Dünya',
    'day'      => 'Bugün {date}',
];
```

**Using :**

```php
$language = new \PHPLanguagesSupport\Language();
$language->setConfig(['path' => 'App/Languages']);

$language->set("en");
echo $language->r("admin.dashboard");
// Output : "Admin Dashboard"

$language->r('admin.who', 'Who are you?');
// Output : "Who are you?"

$language->r('admin.hello', 'Hi World');
// Output : "Hello World"

$language->r('admin.day', null, ['date' => "18/12/2021"]);
// Output : "Today 18/12/2021"

$language->set("tr");
echo $language->r("admin.dashboard");
// Output : "Yönetim Paneli"
```

## License

This library was developed by [Muhammet ŞAFAK](https://www.muhammetsafak.com.tr) and is distributed under the [MIT License](https://github.com/muhametsafak/PHPLanguagesSupport/blob/main/LICENSE).
