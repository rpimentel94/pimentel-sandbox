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
                $gutter = "pt-24 sm:pt-12";
                break;
            case "bottom":
                $gutter = "pb-24 sm:pb-12";
                break;
            case "both":
                $gutter = "py-24 sm:py-12";
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
                $theme_color = '#f5f5f5';
                break;
            default:
                $theme_color = '#f5f5f5';
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

}