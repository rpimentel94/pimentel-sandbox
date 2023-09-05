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

}