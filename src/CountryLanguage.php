<?php
namespace Youssman\CountryLanguage;

class CountryLanguage {

    /**
     * Get an array of language code by the given type.
     *
     * Acceptable language code type parameter values: 1, 2, 3 for returning ISO-639-1, ISO-639-2, ISO-639-3 codes respectively. 
     * If not provided, ISO-639-1 codes will be returned.
     * 
     * @uses CountryLanguage::getLanguageCodes(2) to retrieve the ISO-639-2 language codes.
     * @param string|int $languageCodeType language code type. Acceptable values: 1, 2 or 3.
     * @return array|string returns an array or a string if error
     */
    public static function getLanguageCodes($languageCodeType) {
        $dataPath = '../data.json';
        $data = json_decode(file_get_contents($dataPath));

        $languages = $data['languages'];
        $cTypeNames = ['iso639_1', 'iso639_2en', 'iso639_3'];
        $codes = [];

        if (empty($languageCodeType) || $languageCodeType < 1 || $languageCodeType > count($cTypeNames)) {
            return 'Wrong language code type provided. Valid values: 1, 2, 3 for iso639-1, iso639-2, iso639-3 respectively';
        }

        $cType = $cTypeNames[$languageCodeType - 1];

        foreach ($languages as $key => $language) {
            if ($language[$cType]) {
                $codes[] = $language[$cType];
            }
        }

        return $codes;
    }

    /**
     * Get an array of country code by the given type.
     *
     * Acceptable country code type parameter values: 1, 2, 3 for returning numerical code, alpha-2, alpha-3 codes respectively. 
     * If not provided, alpha-2 codes will be returned.
     * 
     * @uses CountryLanguage::getCountryCodes(2) to retrieve the alpha-2 country codes.
     * @param string|int $countryCodeType country code type. Acceptable values: 1, 2 or 3.
     * @return array|string returns an array or a string if error
     */
    public static function getCountryCodes($countryCodeType) {
        $dataPath = '../data.json';
        $data = json_decode(file_get_contents($dataPath));

        $countries = $data['countries'];
        $cTypeNames = ['numCode', 'code_2', 'code_3'];
        $codes = [];

        if (empty($countryCodeType) || $countryCodeType < 1 || $countryCodeType > count($cTypeNames)) {
            return 'Wrong country code type provided. Valid values: 1, 2, 3 for numeric code, alpha-2, alpha-3 respectively';
        }

        $cType = $cTypeNames[$countryCodeType - 1];

        foreach ($countries as $key => $country) {
            if ($country[$cType]) {
                $codes[] = $country[$cType];
            }
        }

        return $codes;
    }

    /**
     * Check if language code exists.
     *
     * Returns Boolean indicating language existance. 
     * Language code parameter can be either a ISO-639-1, ISO-639-2 or ISO-639-3 code.
     * 
     * @uses CountryLanguage::languageCodeExists('ar')
     * @param string $languageCode language code to check.
     * @return boolean
     */
    public static function languageCodeExists($languageCode) {
        if (empty($languageCode)) {
            return false;
        }
        if (!is_string($languageCode)) {
            return false;
        }

        $exists = false;
        $languageCode = strtolower($languageCode);
        for ($i = 1; $i < 4; $i++) {
            $codes = self::getLanguageCodes($i);
            $exists = in_array($languageCode, $codes);
            if ($exists) {
                break;
            }
        }

        return $exists;
    }

    /**
     * Check if country code exists.
     *
     * Returns Boolean indicating country existance. 
     * Country code parameter can be either an alpha-2, alpha-3 or numerical code.
     * 
     * @uses CountryLanguage::countryCodeExists('MA')
     * @param string $countryCode country code to check.
     * @return boolean
     */
    public static function countryCodeExists($countryCode) {
        if (empty($countryCode)) {
            return false;
        }
        if (!is_string($countryCode)) {
            return false;
        }

        $exists = false;
        $countryCode = strtoupper($countryCode);
        for ($i = 1; $i < 4; $i++) {
            $codes = self::getCountryCodes($i);
            $exists = in_array($countryCode, $codes);
            if ($exists) {
                break;
            }
        }

        return $exists;
    }

    /**
     * Get country from country code
     * 
     * Country code can be either an Alpha-2 or Alpha-3 code. The returned array includes the following info:
     *      - code_2: Country alpha-2 code (2 letters)
     *      - code_3: Country alpha-3 code (3 letters)
     *      - numCode: Country numeric code
     *      - name: Country name
     *      - languages: Array of language for each language spoken in the country
     *      - langCultureMs: Array of language cultures for the country supported by Microsoft©
     * 
     * Each language in languages property includes the following info:
     *      - iso639_1: language iso639-1 code (2 letters)
     *      - iso639_2: language iso639-2 code (3 letters)
     *      - iso639_2en: language iso639-2 code with some codes derived from English names rather than native names of languages (3 letters)
     *      - iso639_3: language iso639-3 code (3 letters)
     *      - name: String array with one or more language names (in English)
     *      - nativeName: String array with one or more language names (in native language)
     *      - direction: Language script direction (either 'LTR' or 'RTL') - Left-to-Right, Right-to-Left
     *      - family: language family
     *      - countries: Array of country where this language is spoken
     * 
     * Each Microsoft© language culture in langCultureMs property icludes the following info:
     *      - langCultureName: language culture name
     *      - displayName: language culture dispaly name
     *      - cultureCode: language culture code
     * 
     * @uses CountryLanguage::getCountry('MA')
     * @param string $countryCode country code.
     * @return array
     */
    public static function getCountry($countryCode, $noLangInfo = false) {
        if (!is_string($countryCode) || empty($countryCode)) {
            return 'No country code provided';
        }

        $countryCode = strtoupper($countryCode);
        $codeFld = '';
        if (strlen($countryCode) == 2) {
            $codeFld = 'code_2';
        } 
        else if (strlen($countryCode) == 3) {
            $codeFld = 'code_3';
        }

        if (empty($codeFld)) {
            return 'Wrong type of country code provided';
        }

        $dataPath = '../data.json';
        $data = json_decode(file_get_contents($dataPath));
        $countries = $data['countries'];

        $country = false;
        foreach ($countries as $c) {
            if ($c[$codeFld] === $countryCode) {
                $country = $c;
                break;
            }
        }

        if (empty($country)) {
            return 'There is no country with code "' + $countryCode + '"';
        }

        if (!$noLangInfo) {
            $langs = $country['languages'];
            $country['languages'] = [];
            foreach ($langs as $key => $lang) {
                $country['languages'][] = self::getLanguage($lang, true);
            }
        }

        return $country;
    }

    /**
     * Get language from language code
     * 
     * Language code can be either iso639-1, iso639-2, iso639-2en or iso639-3 code. 
     * Contents of the returned language are described in getCountry method.
     * 
     * @uses CountryLanguage::getLanguage('ar')
     * @param string $languageCode language code.
     * @return array
     */
    public static function getLanguage($languageCode, $noCountryInfo = false) {
        if (!is_string($countryCode) || empty($countryCode)) {
            return 'No language code provided';
        }

        $languageCode = strtolower($languageCode);
        $codeFld = [];
        if (strlen($languageCode) == 2) {
            $codeFld[] = 'iso639_1';
        } 
        else if (strlen($languageCode) == 3) {
            $codeFld[] = 'iso639_2';
            $codeFld[] = 'iso639_2en';
            $codeFld[] = 'iso639_3';
        }

        if (empty($codeFld)) {
            return 'Wrong type of language code provided';
        }

        $dataPath = '../data.json';
        $data = json_decode(file_get_contents($dataPath));
        $languages = $data['languages'];

        $language = false;
        for ($i = 0; $i < count($codeFld); $i++) {
            foreach ($languages as $l) {
                if ($l[$codeFld[$i]] === $languageCode) {
                    $language = $l;
                    break 2;
                }
            }
        }

        if (empty($language)) {
            return 'There is no language with code "' + $languageCode + '"';
        }

        if (!$noCountryInfo) {
            $countrs = $language['countries'];
            $language['countries'] = [];
            foreach ($countrs as $key => $countr) {
                $language['countries'][] = self::getCountry($countr, true);
            }
        }

        return $language;
    }

    /**
     * Get spoken languages for in a country by given his code
     * 
     * Country code can be either an Alpha-2 or Alpha-3 code. 
     * Each language contains the following info:
     *      - iso639_1: language iso639-1 code (2 letters)
     *      - iso639_2: language iso639-2 code with some codes derived from English names rather than native names of languages (3 letters)
     *      - iso639_3: language iso639-3 code (3 letters)
     * 
     * @uses CountryLanguage::getCountryLanguages('MA')
     * @param string $countryCode country code.
     * @return array
     */
    public static function getCountryLanguages($countryCode) {
        $codes = [];

        $country = self::getCountry($countryCode);
        if (!is_array($country)) {
            return $codes;
        }

        foreach ($country['languages'] as $language) {
            $codes['iso639_1'] = $language['iso639_1'];
            $codes['iso639_2'] = $language['iso639_2en'];
            $codes['iso639_3'] = $language['iso639_3'];
        }

        return $codes;
    }

    /**
     * Get countries where a language is spoken by given his code
     * 
     * Language code can be either iso639-1, iso639-2, iso639-2en or iso639-3 code. 
     * Each Country contains the following info:
     *      - code_2: Country alpha-2 code (2 letters)
     *      - code_3: Country alpha-3 code (3 letters)
     *      - numCode: Country numeric code
     * 
     * @uses CountryLanguage::getLanguageCountries('ar')
     * @param string $languageCode language code.
     * @return array
     */
    public static function getLanguageCountries($languageCode) {
        $codes = [];

        $language = self::getLanguage($languageCode);
        if (!is_array($language)) {
            return $codes;
        }

        foreach ($language['countries'] as $country) {
            $codes['code_2'] = $language['code_2'];
            $codes['code_3'] = $language['code_3'];
            $codes['numCode'] = $language['numCode'];
        }

        return $codes;
    }

    /**
     * Language Cultures info for the country.
     * 
     * Country code can be either an Alpha-2 or Alpha-3 code.
     * Contents of each Language Culture are described in getCountry method.
     * 
     * @uses CountryLanguage::getCountryMsLocales('MA')
     * @param string $countryCode country code.
     * @return array
     */
    public static function getCountryMsLocales($countryCode) {
        $codes = [];

        $country = self::getCountry($countryCode);
        if (!is_array($country)) {
            return $codes;
        }

        $codes = $country['langCultureMs'];

        return $codes;
    }

    /**
     * Language Cultures info for the language.
     * 
     * Language code can be either iso639-1, iso639-2, iso639-2en or iso639-3 code.
     * Contents of each Language Culture are described in getCountry method.
     * 
     * @uses CountryLanguage::getLanguageMsLocales('ar')
     * @param string $languageCode language code.
     * @return array
     */
    public static function getLanguageMsLocales($languageCode) {
        $codes = [];

        $language = self::getLanguage($languageCode);
        if (!is_array($language)) {
            return $codes;
        }

        $codes = $language['langCultureMs'];

        return $codes;
    }

    /**
     * Returns an array with info for every country in the world having an ISO 3166 code.
     * 
     * Contents of each country in the array is described in getCountry method.
     * 
     * @uses CountryLanguage::getCountries()
     * @return array
     */
    public static function getCountries() {
        $dataPath = '../data.json';
        $data = json_decode(file_get_contents($dataPath));

        return $data['countries'];
    }

    /**
     * Returns an array with info for every language in the world having an ISO 639-2 code (and a few more).
     * 
     * Contents of each language in the array is described in .getCountry method.
     * 
     * @uses CountryLanguage::getLanguages()
     * @return array
     */
    public static function getLanguages() {
        $dataPath = '../data.json';
        $data = json_decode(file_get_contents($dataPath));

        return $data['languages'];
    }

    /**
     * Returns an array of strings with the names of each language family.
     * 
     * @uses CountryLanguage::getLanguageFamilies()
     * @return array
     */
    public static function getLanguageFamilies() {
        $dataPath = '../data.json';
        $data = json_decode(file_get_contents($dataPath));

        return $data['languageFamilies'];
    }

    /**
     * Returns an array of strings with all locale codes.
     * 
     * If mode ommited or false, locales with 3 parts will be returned like: az-Cyrl-AZ
     * If mode is set to true, they will be returned like: az-AZ-Cyrl
     * 
     * @uses CountryLanguage::getLocales()
     * @param boolean $mode locale symbols mode.
     * @return array
     */
    public static function getLocales($mode = false) {
        $dataPath = '../data.json';
        $data = json_decode(file_get_contents($dataPath));

        $locales = $data['locales'];
        $ret = [];

        foreach ($locales as $key => $locale) {
            $loc2 = !empty($locale[2]) ? '_' . $locale[2] : '';
            if ($mode) {
                $ret[] = $locale[0] . $loc2 . '_' . $locale[1];
            }
            else {
                $ret[] = $locale[0] . '_' . $locale[1] . $loc2;
            }
        }

        return $ret;
    }

    /**
     * Languages info for each language member in the family.
     * 
     * Returns an array with info for every language in the world having an ISO 639-2 code (and a few more). 
     * Contents of each language in the array is described in getCountry method.
     * Contents of the returned language are described in getCountry method.
     * 
     * @uses CountryLanguage::getLanguageFamilyMembers('Indo-European')
     * @param boolean $family language family name.
     * @return array
     */
    public static function getLanguageFamilyMembers($family) {
        if (empty($family) || !is_string($family)) {
            return 'No language family provided';
        }

        $dataPath = '../data.json';
        $data = json_decode(file_get_contents($dataPath));

        $languages = $data['languages'];
        $ret = [];
        $members = [];
        $check = false;
        $family = strtolower($family);

        foreach ($data['languageFamilies'] as $value) {
            if (strtolower($value) === $family) {
                $check = true;
                break;
            }
        }

        if (!$check) {
            return 'There is no language family "' + $family + '"';
        }

        foreach ($languages as $language) {
            if (strtolower($language['family']) === $family) {
                $members[] = $language;
            }
        }

        foreach ($members as $member) {
            $ret[] = self::getLanguage($member['iso639_3']);
        }

        return $ret;
    }
}