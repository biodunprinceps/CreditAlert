<?php
namespace App\services;

class Helper{

    public static function getBankCode($bankname)
    {
        $bankcode = "000";
        if ($bankname == "STANDARD CHARTERED BANK NIGERIA PLC") {
            $bankcode = "068";
        }

        if ($bankname == "DIAMOND BANK NIGERIA PLC") {
            $bankcode = "063";
        }

        if ($bankname == "FIRST CITY MONUMENT BANK PLC") {
            $bankcode = "214";
        }

        if ($bankname == "UNITY BANK PLC") {
            $bankcode = "215";
        }

        if ($bankname == "STANBIC - IBTC BANK PLC") {
            $bankcode = "221";
        }

        if ($bankname == "STERLING BANK PLC") {
            $bankcode = "232";
        }

        if ($bankname == "JAIZ BANK") {
            $bankcode = "301";
        }

        if ($bankname == "JAIZ BANK PLC") {
            $bankcode = "301";
        }

        if ($bankname == "ACCESS BANK NIGERIA PLC") {
            $bankcode = "044";
        }

        if ($bankname == "ECOBANK NIGERIA PLC") {
            $bankcode = "050";
        }

        if ($bankname == "FIDELITY BANK PLC") {
            $bankcode = "070";
        }

        if ($bankname == "FIRST BANK OF NIGERIA PLC") {
            $bankcode = "011";
        }

        if ($bankname == "GUARANTY TRUST BANK PLC") {
            $bankcode = "058";
        }

        if ($bankname == "HERITAGE BANK") {
            $bankcode = "030";
        }

        if ($bankname == "KEYSTONE BANK PLC") {
            $bankcode = "082";
        }

        if ($bankname == "SKYE BANK PLC") {
            $bankcode = "076";
        }

        if ($bankname == "UNION BANK OF NIGERIA PLC") {
            $bankcode = "032";
        }

        if ($bankname == "UNITED BANK FOR AFRICA PLC") {
            $bankcode = "033";
        }

        if ($bankname == "WEMA BANK PLC") {
            $bankcode = "035";
        }

        if ($bankname == "ZENITH BANK PLC") {
            $bankcode = "057";
        }

        return $bankcode;
    }

    public static function randomString($length = 32, $numeric = false)
    {
        $random_string = "";
        while (strlen($random_string)<$length && $length > 0) {
            if ($numeric === false) {
                $randnum = mt_rand(0, 61);
                $random_string .= ($randnum < 10) ?
                    chr($randnum+48) : ($randnum < 36 ?
                        chr($randnum+55) : $randnum+61);
            } else {
                $randnum = mt_rand(0, 9);
                $random_string .= chr($randnum+48);
            }
        }
        return $random_string;
    }

    public static function startsWith($string, $startString)
    {
        $length = strlen($startString);
        return (substr($string, 0, $length) === $startString);
    }
}
