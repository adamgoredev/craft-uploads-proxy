<?php
/**
 * Uploads Proxy plugin for Craft CMS 3.x
 *
 * Uploads Proxy is a general solution for getting production images on a development server on demand.
 *
 * @link      liftov.be
 * @copyright Copyright (c) 2019 Adam Gore
 */

namespace adamgoredev\uploadsproxy;

use Craft;
use craft\base\Plugin;
use craft\events\DefineAssetThumbUrlEvent;
use craft\events\DefineAssetUrlEvent;
use craft\services\Assets;
use craft\elements\Asset;

use yii\base\Event;

/**
 * Class UploadsProxy
 *
 * Modified to better handle multiple volumes and dynamic folder names
 *
 * @author    Adam Gore
 * @package   UploadsProxy
 * @since     1.0.0
 *
 */
class UploadsProxy extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var UploadsProxy
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public string $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Event::on(
            Assets::class,
            Assets::EVENT_DEFINE_THUMB_URL,
            fn(DefineAssetThumbUrlEvent $event) => $this->processAsset($event),
            null,
            false
        );

        Event::on(
            Asset::class,
            Asset::EVENT_DEFINE_URL,
            fn(DefineAssetUrlEvent $event) => $this->processAsset($event),
            null,
            false
        );

        Craft::info($this->name . ' plugin loaded', __METHOD__);
    }

    private function processAsset($event)
    {
        $remoteSource = getenv("UPLOADS_PROXY_REMOTE");
        $assetBaseFolder = getenv("UPLOADS_PROXY_BASE_FOLDER") ?: '';
        $path = str_replace('@webroot', '', $event->asset->getVolume()->getFs()->path);

        Craft::info("Remote Source: $remoteSource", __METHOD__);
        Craft::info("Asset Path: $path", __METHOD__);

        if ($remoteSource) {
            $filename = $event->asset->path;
            $localeFilePath = trim($assetBaseFolder . '/' . $path, '/') . '/' . $filename;
            $fileDirectory = dirname($localeFilePath);

            Craft::info("Locale File Path: $localeFilePath", __METHOD__);
            Craft::info("Remote File Path: $remoteSource$localeFilePath", __METHOD__);

            if (!file_exists($localeFilePath)) {
                if (!file_exists($fileDirectory)) {
                    mkdir($fileDirectory, 0775, true);
                }

                $remoteFilePath = rtrim($remoteSource, '/') . '/' . ltrim($localeFilePath, '/');
                $remoteFile = @fopen($remoteFilePath, 'r');

                if ($remoteFile) {
                    $success = file_put_contents($localeFilePath, $remoteFile);

                    if ($success === false) {
                        Craft::error("Failed to write file: $localeFilePath", __METHOD__);
                    }
                } else {
                    Craft::error("Could not open remote file: $remoteFilePath", __METHOD__);
                }
            }
        }
    }
}
