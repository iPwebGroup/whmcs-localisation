<?php

define('WHMCS', true);

$langs = [
    'english',
    'greek',
    /*'arabic',
    'azerbaijani',
    'chinese',
    'croatian',
    'czech',
    'danish',
    'dutch',
    'estonian',
    'farsi',
    'french',
    'german',
    'hebrew',
    'hungarian',
    'italian',
    'macedonian',
    'norwegian',
    'portuguese-br',
    'portuguese-pt',
    'romanian',
    'spanish',
    'swedish',
    'turkish',
    'ukranian'*/
];

$globalDiffs = false;

foreach ($langs as $original) {
    foreach ($langs as $comparedTo)
    {
        if ($comparedTo == $original) continue;
        $_LANG = array();

        include('client/' . $original . '.php');
        $originalLangArray = $_LANG;
        unset($_LANG);

        include('client/' . $comparedTo . '.php');
        $comparedToLangArray = $_LANG;

        $diffs = 0;
        foreach ($originalLangArray as $key => $value) {
            parseKeysRecursively(
                $key,
                $value,
                [$key],
                $originalLangArray,
                $comparedToLangArray,
                $original,
                $comparedTo,
                $diffs);
        }

        if ($diffs > 0) {
            echo $original . ' compared to ' . $comparedTo . ' => ' . $diffs . ' diff(s)' . chr(10);
            $globalDiffs = true;
        }
    }
}

if (!$globalDiffs) {
    echo 'No diffs found!' . chr(10);
}

function parseKeysRecursively($key, $value, $keys, $originalArray, $comparedToArray, $original, $comparedTo, &$diffs) {
    if (!array_key_exists($key, $comparedToArray)) {
        echo "\t" . '[' . ucfirst($original) . '][\'' . implode('\'][\'', $keys) . '\']';
        echo  ' => '. ucfirst(gettype($originalArray[$key])) . ' not in [' . ucfirst($comparedTo) . ']' . chr(10);
        $diffs++;
    } elseif (is_array($value)) {
        foreach ($value as $subKey => $subValue) {
            parseKeysRecursively($subKey,
                $subValue,
                array_merge($keys, [$subKey]),
                $originalArray[$key],
                $comparedToArray[$key],
                $original,
                $comparedTo,
                $diffs);
        }
    }
}