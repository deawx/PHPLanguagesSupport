<?php
/**
 * Language.php
 *
 * This file is part of PHPLanguagesSupport.
 *
 * @author     Muhammet ŞAFAK <info@muhammetsafak.com.tr>
 * @copyright  Copyright © 2021 PHPLanguagesSupport
 * @license    https://github.com/muhametsafak/PHPLanguagesSupport/blob/main/LICENSE MIT
 * @version    1.0
 * @link       https://www.muhammetsafak.com.tr
 */

declare(strict_types=1);

namespace PHPLanguagesSupport;

use \Exception;

class Language
{
    protected static ?string $get = null;

    protected static array $config = [
        'path'  => __DIR__,
        'base'  => 'main'
    ];

    protected static array $lang = [];

    /**
     * Dil Desteği kütüphanesinin ayarlarını yapılandırmanızı sağlar.
     *
     * @param array $configs <p><pre>$configs = [
     * "path" => __DIR__, "base" => "main"
     * ]</pre>
     * Path; Dil dizinlerinin aranacağı ana dizinin yolu.
     * Base; Büyük/küçük harf duyarsız ana dil dosyasının adı.
     * </p>
     * @return void
     */
    public function setConfig(array $configs): void
    {
        self::$config = \array_merge(self::$config, $configs);
    }

    /**
     * Geçerli dili değiştirir ve yükler.
     *
     * @param string $language <p>Dil dosyalarını tutan klasörün adı.</p>
     * @return bool <p>Dil dosyaları başarıyla yüklendi ya da daha önce yüklenmiş ise <code>true<code>, aksi halde <code>false</code> döndürür.<p>
     * @throws Exception <p>Belirtilen dil için yükleme dizini bulamazsa hata fırlatır.</p>
     */
    public function set(string $language): bool
    {
        self::$get = $language;
        return $this->load();
    }

    /**
     * Geçerli dili döndürür.
     *
     * @return string|null
     */
    public function get(): ?string
    {
        return self::$get;
    }

    /**
     * Geçerli dil dizisinden istenilen elemanın değerine ulaşır. Ve döndürür.
     *
     * @see Language::rStatic()
     * @param string $key <p>Geçerli dil dizisinde istenilen değerin anahtarı</p>
     * @param string|null $default <p>Geçerli dil dizisinde istenilen eleman bulunaması durumunda yerine kullanılacak varsayılan değer.</p>
     * @param array $context <p>Dizge içerisinde yer tutucu/değişken alanların doldurulması için kullanılacak ilişkisel dizi.</p>
     * @return string
     */
    public function r(string $key, ?string $default = null, array $context = []): string
    {
        return self::rStatic($key, $default, $context);
    }

    /**
     * Geçerli dil dizisinden istenilen elemanın değerine ulaşır. Ve döndürür.
     *
     * @param string $key <p>Geçerli dil dizisinde istenilen değerin anahtarı</p>
     * @param string|null $default <p>Geçerli dil dizisinde istenilen eleman bulunaması durumunda yerine kullanılacak varsayılan değer.</p>
     * @param array $context <p>Dizge içerisinde yer tutucu/değişken alanların doldurulması için kullanılacak ilişkisel dizi.</p>
     * @return string
     */
    public static function rStatic(string $key, ?string $default = null, array $context = []): string
    {
        if(\strpos($key, '.') !== FALSE){
            [$base, $baseKey] = \explode('.', $key, 2);
        }else{
            [$base, $baseKey] = [self::$config['base'], $key];
        }
        $base = \strtolower($base);
        $baseKey = \strtolower($base);
        if($default === null){
            $default = $key;
        }
        $r = self::$lang[self::$get][$base][$baseKey] ?? $default;
        if(!empty($context)){
            return self::interpolate($r, $context);
        }
        return $r;
    }

    protected static function interpolate(string $message, array $context = []): string
    {
        $replace = [];
        $i = 0;
        foreach ($context as $key => $val) {
            if (!\is_array($val) && (!\is_object($val) || \method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
                $replace['{' . $i . '}'] = $val;
                $i++;
            }
        }
        return \strtr($message, $replace);
    }

    /**
     * @return bool
     * @throws Exception <p>Geçerli dil için dizin yolu bulunamazsa bir hata fırlatır.</p>
     */
    protected function load(): bool
    {
        $get = self::$get;
        if(isset(self::$lang[$get])){
            return true;
        }
        self::$lang[$get] = [];

        $path = \rtrim((self::$config['path'] ?? ''), '/') . \DIRECTORY_SEPARATOR . $get . \DIRECTORY_SEPARATOR;
        if(!\is_dir($path)){
            throw new Exception('Could not find directory "' . $path . '".');
        }

        if(\is_array($files = \glob($path . '*.php'))){
            foreach($files as $file){
                $basename = \basename($file, '.php');
                self::$lang[$get][\strtolower($basename)] = $this->fileLoad($file);
            }
            return true;
        }
        return false;
    }

    /**
     * @param string $path
     * @return array
     * @throws Exception <p>Belirtilen yolda bir dosya bulunamazsa hata fırlatır.</p>
     */
    protected function fileLoad(string $path)
    {
        if(!\is_file($path)){
            throw new Exception($path . " is not found.");
        }
        return require_once($path);
    }

}
