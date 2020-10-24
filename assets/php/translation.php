<?php
namespace Translation;

use Exception;

class Translation
{
    /**
     * Directory path of the locale files.
     * @type string
     */
    protected static $localesDir = 'assets/locales';

    /**
     * The default language to use if the client language or the forced language locales are not found.
     * @type string
     */
    protected static $defaultLanguage = 'en-US';

    /**
     * Array where loaded locales will be kept to avoid the need of reload the locale file.
     * @type array
     */
    protected static $loadedLocales = array();

    /**
     * The current language being used to translate.
     * @type string
     */
    protected static $currentLanguage = '';

    /**
     * Determine whether the class has been initialized.
     * @type bool
     */
    protected static $initialized = false;

    /**
     * Definition of the enclosing characters for the parameters inside the translation strings.
     * @type array
     */
    protected static $parameterEnclosingChars = array('{', '}');

    /**
     * Returns the translation of a specific key from the current language locale.
     * Optionally you can fill the parameters array with the corresponding string replacements.
     * @param string $localeKey
     * @param array $parameters
     * @return string|null
     * @throws Exception
     */
    public static function of($localeKey, $parameters = array())
    {
        static::checkIfHasBeenInitialized();
        static::checkIfLocalesAreLoaded();

        if (is_string($localeKey) && !empty(static::$loadedLocales[static::$currentLanguage][$localeKey]))
        {
            $text = static::$loadedLocales[static::$currentLanguage][$localeKey];

            if (!empty($parameters) && is_array($parameters))
            {
                foreach ($parameters as $parameter => $replacement)
                {
                    $text = str_replace(static::$parameterEnclosingChars[0] . $parameter . static::$parameterEnclosingChars[1], $replacement, $text);
                }
            }

            return $text;
        }

        return null;
    }

    /**
     * Used to set a custom locale directory.
     * @param string $localesDir
     */
    public static function setLocalesDir($localesDir)
    {
        if (!empty($localesDir) && is_string($localesDir))
        {
            self::$localesDir = $localesDir;
            static::checkIfLocalesDirExists();
        }
    }

    /**
     * Used to set a custom default language.
     * @param string $defaultLanguage
     */
    public static function setDefaultLanguage($defaultLanguage)
    {
        if (!empty($defaultLanguage) && is_string($defaultLanguage))
        {
            self::$defaultLanguage = $defaultLanguage;
            static::checkIfLocaleForDefaultLanguageExists();
        }
    }

    /**
     * Used to force the translations in a specific language from now on.
     * To cancel the enforcement, at any time, you can call "forceLanguage(null)",
     * then it restarts using the client language or the default language.
     * @param string $language
     */
    public static function forceLanguage($language)
    {
        static::checkIfHasBeenInitialized();
        static::$currentLanguage = (!empty($language) && is_string($language)) ? $language : static::getClientLanguage();
        static::checkIfLocalesAreLoaded();
    }

    /**
     * Used to set custom parameter enclosing characters.
     * @param string $openingChar
     * @param string $closingChar
     */
    public static function setParameterEnclosingChars($openingChar = '{', $closingChar = '}')
    {
        if (!empty($openingChar) && is_string($openingChar) && !empty($closingChar) && is_string($closingChar))
        {
            static::$parameterEnclosingChars = array($openingChar, $closingChar);
        }
    }

    /**
     * Checks if current language locale is already loaded. Loading new locales only when needed.
     * @throws Exception
     */
    protected static function checkIfLocalesAreLoaded()
    {
        if (empty(static::$loadedLocales[static::$currentLanguage]))
        {
            static::checkIfLocaleForCurrentLanguageExists();
            static::$loadedLocales[static::$currentLanguage] = require(static::$localesDir . '/' . static::$currentLanguage . '.php');
        }
    }

    /**
     * Returns the client language code.
     * @return string|null Returns the ISO-639 Language Code followed by ISO-3166 Country Code, like 'en-US'. Null if PHP couldn't detect it.
     */
    protected static function getClientLanguage()
    {
        return !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 5) : null;
    }

    /**
     * Checks if locale dir exists. If not, throws an exception.
     * @throws Exception
     */
    protected static function checkIfLocalesDirExists()
    {
        if (!is_dir(static::$localesDir))
        {
            throw new Exception('Directory "' . static::$localesDir . '" not found!');
        }
    }

    /**
     * Checks if locale for default language exists. If not, throws an exception.
     * @throws Exception
     */
    protected static function checkIfLocaleForDefaultLanguageExists()
    {
        if (!file_exists(static::$localesDir . '/' . static::$defaultLanguage . '.php'))
        {
            throw new Exception('Default language locale not found!');
        }
    }

    /**
     * Checks if the class has been initialized.
     */
    protected static function checkIfHasBeenInitialized()
    {
        if (!static::$initialized)
        {
            static::initialize();
        }
    }

    /**
     * Initializes the class, setting up the default configuration.
     * @throws Exception
     */
    protected static function initialize()
    {
        static::checkIfLocalesDirExists();
        static::checkIfLocaleForDefaultLanguageExists();
        static::$currentLanguage = static::getClientLanguage();
        static::$initialized = true;
    }

    /**
     * Checks if locale for current language exists. If not, start using the default language.
     */
    protected static function checkIfLocaleForCurrentLanguageExists()
    {
        if (!file_exists(static::$localesDir . '/' . static::$currentLanguage . '.php'))
        {
            static::$currentLanguage = static::$defaultLanguage;
        }
    }
}
