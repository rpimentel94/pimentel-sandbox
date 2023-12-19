<?php

namespace Drupal\tailwind\Plugin\Preprocess;

use Drupal\media\Entity\Media;
use Drupal\image\Entity\ImageStyle;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\preprocess\PreprocessPluginBase;

/**
 * Helper Class to Parse Field Data
 *
 */
class TailwindHelper
{

    /**
     * {@inheritdoc}
     */
    public static function getAlignment(string $field_alignment): string
    {
        $alignment = "";

        switch ($field_alignment) {
            case "none":
                $alignment = "";
                break;
            case "left":
                $alignment = "justify-start";
                break;
            case "right":
                $alignment = "justify-end";
                break;
            case "center":
                $alignment = "justify-center";
                break;
            default:
                $alignment = "";
        }

        return $alignment;
    }

    /**
     * {@inheritdoc}
     */
    public static function getGutter(string $field_gutter): string
    {
        $gutter = "";

        switch ($field_gutter) {
            case "none":
                $gutter = "";
                break;
            case "top":
                $gutter = "pt-24";
                break;
            case "bottom":
                $gutter = "pb-24";
                break;
            case "both":
                $gutter = "py-24";
                break;
            default:
                $gutter = "";
        }

        return $gutter;
    }

    public static function getColor(string $color): string
    {
        $theme_color = "";
        switch ($color) {
            case 'primary':
                $theme_color = theme_get_setting('primary_color');
                break;
            case 'secondary':
                $theme_color = theme_get_setting('secondary_color');
                break;
            case 'grey':
                $theme_color = 'htlfLighterGray';
                break;
            default:
                $theme_color = 'htlfLighterGray';
                break;
        }

        return $theme_color;
    }

    public static function getButtonColor(string $background_color): array
    {
        $color = [];

        switch ($background_color) {
            case 'primary':
                $color['background'] = theme_get_setting('primary_color');
                $color['text'] = "text-htlfWhite border border-white border-solid hover:!bg-htlfWhite hover:!text-htlfBlack";
                break;
            case 'secondary':
                $color['background'] = "bg-htlfWhite";
                $color['text'] = "bg-htlfWhite text-htlfBlack hover:!bg-htlfBlue hover:!text-htlfWhite";
                break;
            case 'htlfBody':
                $color['background'] = theme_get_setting('primary_color');
                $color['text'] = "text-htlfWhite";
                break;
            default:
                $color['background'] = theme_get_setting('primary_color');
                $color['text'] = "text-htlfWhite";
                break;
        }

        return $color;
    }

    public static function getGridButtonColor(string $background_color, string $style): array
    {
        $color = [];

        if ($style == "callouts") {
            $color['background'] = theme_get_setting('primary_color');
            $color['text'] = "rounded-md w-44 top-5 py-4 px-10 my-8 font-medium hover:shadow-2xl hover:drop-shadow-2xl transition duration-200 text-htlfWhite border border-white border-solid hover:!bg-htlfWhite hover:!text-htlfBlack";
        } elseif ($style == "smallcallouts") {
            $color['background'] = "";
            $color['text'] = "text-htlfWhite hover:!text-htlfLighterGray";
        } else {
            $color['background'] = "";
            $color['text'] = "text-[--PrimaryColor] font-medium hover:underline underline-offset-8 decoration-2 decoration-[--PrimaryColor]";
            $color['chevron'] = "border-[--PrimaryColor]";
        }

        return $color;
    }

    /**
     * Create a URL object.
     *
     * Returns a URL object with some exception handling to prevent
     * errors.
     *
     * @param string $url
     *   The URL string.
     *
     * @return \Drupal\Core\Url
     *   The Drupal Url object.
     */
    public static function createUrl(string $text, string $link, Url $url)
    {
        try {
            $url = Url::fromUri($link);
        } catch (\InvalidArgumentException $exception) {
            $link = Link::fromTextAndUrl($text, $url);
            $url = $link->getUrl()->toString();
        }
        return $url;
    }


}