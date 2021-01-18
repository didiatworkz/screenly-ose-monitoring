# How to use the Translation CLASS
First you need to include the class:
```php
require_once('translation.php');
use Translation\Translation;
```

Then you need to set the path to the assets/locales Folder
```php
Translation::setLocalesDir(__DIR__ . '/../locales');
```

Now you can use different methods to replace the string in the PHP script:
```php
'.Translation::of('variable').'
Translation::of('variable');
Translation::of('variable.subvariable');
Translation::of('variable.subvariable', ['name' => $name]);
```

## Why doesn't it show me my language
The translation class is based on the language set in the browser. If the language is not displayed, it may be because it does not yet exist or the browser specifies the wrong language.


If you want to set a language by default, you have to enter the language syntax in line 30 of the file `assets\php\translation.php`
