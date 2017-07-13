<?php

use Bitmovin\api\ApiClient;
use Bitmovin\api\enum\AclPermission;
use Bitmovin\api\enum\manifests\dash\DashMuxingType;
use Bitmovin\api\enum\manifests\hls\MediaInfoType;
use Bitmovin\api\enum\Status;
use Bitmovin\api\model\encodings\helper\Acl;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use Bitmovin\api\model\manifests\dash\AudioAdaptationSet;
use Bitmovin\api\model\manifests\dash\DashManifest;
use Bitmovin\api\model\manifests\dash\DashRepresentation;
use Bitmovin\api\model\manifests\dash\Period;
use Bitmovin\api\model\manifests\dash\VideoAdaptationSet;
use Bitmovin\api\model\manifests\hls\HlsManifest;
use Bitmovin\api\model\manifests\hls\MediaInfo;
use Bitmovin\api\model\manifests\hls\StreamInfo;
use Bitmovin\api\model\outputs\GcsOutput;

require_once __DIR__ . '/vendor/autoload.php';

$startSegment = intval($_GET['start']);
$endSegment = intval($_GET['end']);

if ($startSegment < 0)
    $startSegment = 0;
if ($endSegment < 0)
    $endSegment = 0;

$encodingInformation = json_decode(file_get_contents('encodingInformation.json'), true);

// INPUT INFORMATION FROM LIVE STREAM
$encoding_id = $encodingInformation['ENCODING_ID'];
$stream_audio_id = $encodingInformation['AUDIO_STREAM_ID'];
$stream_1080p_id = $encodingInformation['1080_STREAM_ID'];
$stream_720p_id = $encodingInformation['720_STREAM_ID'];
$stream_480p_id = $encodingInformation['480_STREAM_ID'];
$stream_360_id = $encodingInformation['360_STREAM_ID'];
$stream_240_id = $encodingInformation['240_STREAM_ID'];
$fmp4_muxing_audio_id = $encodingInformation['FMP4_AUDIO_MUXING_ID'];
$fmp4_muxing_1080p_id = $encodingInformation['FMP4_1080_MUXING_ID'];
$fmp4_muxing_720p_id = $encodingInformation['FMP4_720_MUXING_ID'];
$fmp4_muxing_480p_id = $encodingInformation['FMP4_480_MUXING_ID'];
$fmp4_muxing_360p_id = $encodingInformation['FMP4_360_MUXING_ID'];
$fmp4_muxing_240p_id = $encodingInformation['FMP4_240_MUXING_ID'];

$config = json_decode(file_get_contents('config.json'), true);

// CREATE API CLIENT
$apiClient = new ApiClient($config['API_KEY']);

// CONFIGURATION
$gcs_accessKey = $config['GCS_ACCESS_KEY'];
$gcs_secretKey = $config['GCS_SECRET_KEY'];
$gcs_bucketName = $config['GCS_BUCKET_NAME'];
$gcs_prefix = $config['GCS_PREFIX'];

$manifestPostfix = time() . '_' . uniqid();

// CREATE OUTPUT
$output = new GcsOutput($gcs_bucketName, $gcs_accessKey, $gcs_secretKey);
$output = $apiClient->outputs()->create($output);

// CREATE DASH MANIFEST
$manifestOutput = new EncodingOutput($output);
$manifestOutput->setOutputPath($gcs_prefix);
$manifestOutput->setAcl(array(new Acl(AclPermission::ACL_PUBLIC_READ)));

$dashManifest = new DashManifest();
$dashManifest->setName("stream_vod_$manifestPostfix.mpd");
$dashManifest->setManifestName("stream_vod_$manifestPostfix.mpd");
$dashManifest->setOutputs(array($manifestOutput));
$dashManifest = $apiClient->manifests()->dash()->create($dashManifest);

$period = new Period();
$period = $apiClient->manifests()->dash()->createPeriod($dashManifest, $period);

$videoAdaptationSet = new VideoAdaptationSet();
$videoAdaptationSet = $apiClient->manifests()->dash()->addVideoAdaptionSetToPeriod($dashManifest, $period, $videoAdaptationSet);
$audioAdaptationSet = new AudioAdaptationSet();
$audioAdaptationSet->setLang('en');
$audioAdaptationSet = $apiClient->manifests()->dash()->addAudioAdaptionSetToPeriod($dashManifest, $period, $audioAdaptationSet);

$audioRepresentation = new DashRepresentation();
$audioRepresentation->setType(DashMuxingType::TYPE_TEMPLATE);
$audioRepresentation->setEncodingId($encoding_id);
$audioRepresentation->setMuxingId($fmp4_muxing_audio_id);
$audioRepresentation->setSegmentPath('audio/128kbps_dash');
$audioRepresentation->setStartSegmentNumber($startSegment);
$audioRepresentation->setEndSegmentNumber($endSegment);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $period, $audioAdaptationSet, $audioRepresentation);

$videoRepresentation_1080p = new DashRepresentation();
$videoRepresentation_1080p->setType(DashMuxingType::TYPE_TEMPLATE);
$videoRepresentation_1080p->setEncodingId($encoding_id);
$videoRepresentation_1080p->setMuxingId($fmp4_muxing_1080p_id);
$videoRepresentation_1080p->setSegmentPath('video/1080p_dash');
$videoRepresentation_1080p->setStartSegmentNumber($startSegment);
$videoRepresentation_1080p->setEndSegmentNumber($endSegment);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $period, $videoAdaptationSet, $videoRepresentation_1080p);

$videoRepresentation_720p = new DashRepresentation();
$videoRepresentation_720p->setType(DashMuxingType::TYPE_TEMPLATE);
$videoRepresentation_720p->setEncodingId($encoding_id);
$videoRepresentation_720p->setMuxingId($fmp4_muxing_720p_id);
$videoRepresentation_720p->setSegmentPath('video/720p_dash');
$videoRepresentation_720p->setStartSegmentNumber($startSegment);
$videoRepresentation_720p->setEndSegmentNumber($endSegment);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $period, $videoAdaptationSet, $videoRepresentation_720p);

$videoRepresentation_480p = new DashRepresentation();
$videoRepresentation_480p->setType(DashMuxingType::TYPE_TEMPLATE);
$videoRepresentation_480p->setEncodingId($encoding_id);
$videoRepresentation_480p->setMuxingId($fmp4_muxing_480p_id);
$videoRepresentation_480p->setSegmentPath('video/480p_dash');
$videoRepresentation_480p->setStartSegmentNumber($startSegment);
$videoRepresentation_480p->setEndSegmentNumber($endSegment);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $period, $videoAdaptationSet, $videoRepresentation_480p);

$videoRepresentation_360p = new DashRepresentation();
$videoRepresentation_360p->setType(DashMuxingType::TYPE_TEMPLATE);
$videoRepresentation_360p->setEncodingId($encoding_id);
$videoRepresentation_360p->setMuxingId($fmp4_muxing_360p_id);
$videoRepresentation_360p->setSegmentPath('video/360p_dash');
$videoRepresentation_360p->setStartSegmentNumber($startSegment);
$videoRepresentation_360p->setEndSegmentNumber($endSegment);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $period, $videoAdaptationSet, $videoRepresentation_360p);

$videoRepresentation_240p = new DashRepresentation();
$videoRepresentation_240p->setType(DashMuxingType::TYPE_TEMPLATE);
$videoRepresentation_240p->setEncodingId($encoding_id);
$videoRepresentation_240p->setMuxingId($fmp4_muxing_240p_id);
$videoRepresentation_240p->setSegmentPath('video/240p_dash');
$videoRepresentation_240p->setStartSegmentNumber($startSegment);
$videoRepresentation_240p->setEndSegmentNumber($endSegment);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $period, $videoAdaptationSet, $videoRepresentation_240p);

$apiClient->manifests()->dash()->start($dashManifest);

do
{
    $status = $apiClient->manifests()->dash()->status($dashManifest);
    $isRunning = !in_array($status->getStatus(), array(Status::ERROR, Status::FINISHED));
    sleep(0.25);
} while ($isRunning);

$manifests = array(
    'DASH' => $config['HTTP_ROOT_PATH'] . "stream_vod_$manifestPostfix.mpd"
);

echo json_encode($manifests);
