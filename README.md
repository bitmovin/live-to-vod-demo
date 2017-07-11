# Live-to-VoD Demo
# [![bitmovin](https://cloudfront-prod.bitmovin.com/wp-content/themes/Bitmovin-V-0.1/images/logo3.png)](http://www.bitmovin.com)
This demo shows how to start a live stream and then creating a VoD manifests from the live stream using the [Bitmovin API](https://bitmovin.com/bitmovins-video-api/) and a Web-Application.

## Structure of the Demo
- `css` - CSS folder
- `fonts` - Fonts folder
- `images` - Images folder
- `js` - JavaScript folder
- `player-ui` - Custom player UI
- `composer.json` - Composer file
- `config.json` - The config file with the API-Key, Bucket credentials, live stream settings, ...
- `getLiveManifest.php` - Script which will be called to get the live manifest
- `getVodManifest.php` - Script which will be called to get the VoD manifest
- `index.php` - Web-Application
- `startLiveStream.php` - Script hat must be run by the user to start a Live-Stream

## Getting started
First make sure that you have an API-Key and a player key. You can get an API-Key [here](https://bitmovin.com/bitmovins-video-api/)

* Install the Bitmovin-PHP client by running `composer install` in the directory (further information can be found [here](https://github.com/bitmovin/bitmovin-php))
* Set constant `playerKey` in `js/liveToVodFunctions.js` to your player key
* Adapt `config.js`
```
API_KEY - Please enter your API Key
GCS_ACCESS_KEY - Access Key of your GCS storage
GCS_SECRET_KEY - Secret Key of your GCS storage
GCS_BUCKET_NAME - Bucket name of your GCS storage
GCS_PREFIX - Prefix path on your bucket where the live encoding should be written to
STREAM_KEY - Stream key to use for ingesting
HTTP_ROOT_PATH - HTTP url to the folder on the bucket 
```
Example:
```json
{
  "API_KEY": "12345678-90ab-cdef-1234-567890abcdef",
  "GCS_ACCESS_KEY": "GCS-ACCESS-KEY",
  "GCS_SECRET_KEY": "GCS-SECRET-KEY",
  "GCS_BUCKET_NAME": "my-gcs-bucket",
  "GCS_PREFIX": "path/to/my/test/folder/",
  "STREAM_KEY": "bitmovin",
  "HTTP_ROOT_PATH" : "https://storage.googleapis.com/my-gcs-bucket/path/to/my/test/folder/"
}
```
* Run `startLiveStream.php` and wait for the script to finish. 
* You can now ingest into the RTMP URL provided by the `startLiveStream.php` script
