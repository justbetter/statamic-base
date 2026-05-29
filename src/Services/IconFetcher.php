<?php

namespace JustBetter\StatamicBase\Services;

class IconFetcher
{
    public const FALLBACK_ICON = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>';

    public function fetch(): string
    {
        $url = trim(config()->string('justbetter.statamic-base.icon_url'));

        if ($url === '' || filter_var($url, FILTER_VALIDATE_URL) === false) {
            return self::FALLBACK_ICON;
        }

        $darkRaw = config('justbetter.statamic-base.icon_dark_url');
        $darkUrl = is_string($darkRaw) ? trim($darkRaw) : '';

        if ($darkUrl !== '' && filter_var($darkUrl, FILTER_VALIDATE_URL) !== false) {
            return $this->svgImagesForLightAndDark($url, $darkUrl);
        }

        return $this->svgImageFromUrl($url);
    }

    protected function svgImagesForLightAndDark(string $lightUrl, string $darkUrl): string
    {
        $light = htmlspecialchars($lightUrl, ENT_QUOTES | ENT_XML1, 'UTF-8');
        $dark = htmlspecialchars($darkUrl, ENT_QUOTES | ENT_XML1, 'UTF-8');

        return '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 300 200" role="img" aria-hidden="true">'
            .'<g class="dark:hidden">'.$this->imageElement($light).'</g>'
            .'<g class="hidden dark:block">'.$this->imageElement($dark).'</g>'
            .'</svg>';
    }

    protected function svgImageFromUrl(string $url): string
    {
        $href = htmlspecialchars($url, ENT_QUOTES | ENT_XML1, 'UTF-8');

        return '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 300 200" role="img" aria-hidden="true">'.$this->imageElement($href).'</svg>';
    }

    protected function imageElement(string $escapedHref): string
    {
        return '<image width="300" height="200" x="0" y="0" href="'.$escapedHref.'" xlink:href="'.$escapedHref.'" preserveAspectRatio="xMidYMid meet" />';
    }
}
