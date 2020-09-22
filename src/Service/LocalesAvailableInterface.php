<?php

declare(strict_types=1);

namespace mindtwo\Service\LaravelMissingTranslations;

interface LocalesAvailableInterface
{
    function getMainLocale() : string;
    function getAllLocales() : array;
}
