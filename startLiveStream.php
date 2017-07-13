<?php

use Bitmovin\api\ApiClient;
use Bitmovin\api\enum\AclPermission;
use Bitmovin\api\enum\CloudRegion;
use Bitmovin\api\enum\Status;
use Bitmovin\api\enum\codecConfigurations\H264Profile;
use Bitmovin\api\enum\SelectionMode;
use Bitmovin\api\exceptions\BitmovinException;
use Bitmovin\api\model\codecConfigurations\AACAudioCodecConfiguration;
use Bitmovin\api\model\codecConfigurations\H264VideoCodecConfiguration;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\helper\Acl;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use Bitmovin\api\model\encodings\helper\InputStream;
use Bitmovin\api\model\encodings\muxing\FMP4Muxing;
use Bitmovin\api\model\encodings\muxing\MuxingStream;
use Bitmovin\api\model\encodings\streams\Stream;
use Bitmovin\api\model\manifests\dash\DashManifest;
use Bitmovin\api\model\manifests\dash\Period;
use Bitmovin\api\model\outputs\GcsOutput;
use Bitmovin\api\enum\manifests\dash\DashMuxingType;
use Bitmovin\api\model\encodings\LiveDashManifest;
use Bitmovin\api\model\manifests\dash\AudioAdaptationSet;
use Bitmovin\api\model\manifests\dash\DashRepresentation;
use Bitmovin\api\model\manifests\dash\VideoAdaptationSet;

require_once __DIR__ . '/vendor/autoload.php';

$config = json_decode(file_get_contents('config.json'), true);

// CREATE API CLIENT
$apiClient = new ApiClient($config['API_KEY']);

// CONFIGURATION
$gcs_accessKey = $config['GCS_ACCESS_KEY'];
$gcs_secretKey = $config['GCS_SECRET_KEY'];
$gcs_bucketName = $config['GCS_BUCKET_NAME'];
$gcs_prefix = $config['GCS_PREFIX'];
$stream_key = $config['STREAM_KEY'];

// CREATE ENCODING
$encoding = new Encoding('PHP LIVE STREAM');
$encoding->setCloudRegion(CloudRegion::GOOGLE_EUROPE_WEST_1);
$encoding->setDescription('PHP LIVE STREAM');
$encoding = $apiClient->encodings()->create($encoding);

// GET RTMP INPUT
$input = $apiClient->inputs()->rtmp()->listPage()[0];

// CREATE OUTPUT
$output = new GcsOutput($gcs_bucketName, $gcs_accessKey, $gcs_secretKey);
$output = $apiClient->outputs()->create($output);

// CREATE VIDEO STREAM FOR 1080p
$videoConfig1080p = new H264VideoCodecConfiguration('StreamDemo1080p', H264Profile::HIGH, 4800000, 30.0);
$videoConfig1080p->setDescription('StreamDemo1080p');
$videoConfig1080p->setWidth(1920);
$videoConfig1080p->setHeight(1080);
$videoConfig1080p = $apiClient->codecConfigurations()->videoH264()->create($videoConfig1080p);
$inputStream1080p = new InputStream($input, 'live', SelectionMode::AUTO);
$inputStream1080p->setPosition(0);
$stream1080p = new Stream($videoConfig1080p, array($inputStream1080p));
$stream1080p = $apiClient->encodings()->streams($encoding)->create($stream1080p);

// CREATE MUXING FOR 1080p
$encodingOutput1080p = new EncodingOutput($output);
$encodingOutput1080p->setOutputPath($gcs_prefix . 'video/1080p_dash');
$encodingOutput1080p->setAcl(array(new Acl(AclPermission::ACL_PUBLIC_READ)));
$muxingStream1080p = new MuxingStream();
$muxingStream1080p->setStreamId($stream1080p->getId());
$fmp4Muxing1080p = new FMP4Muxing();
$fmp4Muxing1080p->setInitSegmentName('init.mp4');
$fmp4Muxing1080p->setSegmentLength(1);
$fmp4Muxing1080p->setSegmentNaming('segment_%number%.m4s');
$fmp4Muxing1080p->setOutputs(array($encodingOutput1080p));
$fmp4Muxing1080p->setStreams(array($muxingStream1080p));
$fmp4Muxing1080p = $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->create($fmp4Muxing1080p);

// CREATE VIDEO STREAM FOR 720p
$videoConfig720p = new H264VideoCodecConfiguration('StreamDemo720p', H264Profile::HIGH, 2400000, 30.0);
$videoConfig720p->setDescription('StreamDemo720p');
$videoConfig720p->setWidth(1280);
$videoConfig720p->setHeight(720);
$videoConfig720p = $apiClient->codecConfigurations()->videoH264()->create($videoConfig720p);
$inputStream720p = new InputStream($input, 'live', SelectionMode::AUTO);
$inputStream720p->setPosition(0);
$stream720p = new Stream($videoConfig720p, array($inputStream720p));
$stream720p = $apiClient->encodings()->streams($encoding)->create($stream720p);

// CREATE MUXING FOR 720p
$encodingOutput720p = new EncodingOutput($output);
$encodingOutput720p->setOutputPath($gcs_prefix . 'video/720p_dash');
$encodingOutput720p->setAcl(array(new Acl(AclPermission::ACL_PUBLIC_READ)));
$muxingStream720p = new MuxingStream();
$muxingStream720p->setStreamId($stream720p->getId());
$fmp4Muxing720p = new FMP4Muxing();
$fmp4Muxing720p->setInitSegmentName('init.mp4');
$fmp4Muxing720p->setSegmentLength(1);
$fmp4Muxing720p->setSegmentNaming('segment_%number%.m4s');
$fmp4Muxing720p->setOutputs(array($encodingOutput720p));
$fmp4Muxing720p->setStreams(array($muxingStream720p));
$fmp4Muxing720p = $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->create($fmp4Muxing720p);

// CREATE VIDEO STREAM FOR 480p
$videoConfig480p = new H264VideoCodecConfiguration('StreamDemo480p', H264Profile::HIGH, 1200000, 30.0);
$videoConfig480p->setDescription('StreamDemo480p');
$videoConfig480p->setWidth(858);
$videoConfig480p->setHeight(480);
$videoConfig480p = $apiClient->codecConfigurations()->videoH264()->create($videoConfig480p);
$inputStream480p = new InputStream($input, 'live', SelectionMode::AUTO);
$inputStream480p->setPosition(0);
$stream480p = new Stream($videoConfig480p, array($inputStream480p));
$stream480p = $apiClient->encodings()->streams($encoding)->create($stream480p);

// CREATE MUXING FOR 480p
$encodingOutput480p = new EncodingOutput($output);
$encodingOutput480p->setOutputPath($gcs_prefix . 'video/480p_dash');
$encodingOutput480p->setAcl(array(new Acl(AclPermission::ACL_PUBLIC_READ)));
$muxingStream480p = new MuxingStream();
$muxingStream480p->setStreamId($stream480p->getId());
$fmp4Muxing480p = new FMP4Muxing();
$fmp4Muxing480p->setInitSegmentName('init.mp4');
$fmp4Muxing480p->setSegmentLength(1);
$fmp4Muxing480p->setSegmentNaming('segment_%number%.m4s');
$fmp4Muxing480p->setOutputs(array($encodingOutput480p));
$fmp4Muxing480p->setStreams(array($muxingStream480p));
$fmp4Muxing480p = $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->create($fmp4Muxing480p);

// CREATE VIDEO STREAM FOR 360p
$videoConfig360p = new H264VideoCodecConfiguration('StreamDemo360p', H264Profile::HIGH, 800000, 30.0);
$videoConfig360p->setDescription('StreamDemo360p');
$videoConfig360p->setWidth(640);
$videoConfig360p->setHeight(360);
$videoConfig360p = $apiClient->codecConfigurations()->videoH264()->create($videoConfig360p);
$inputStream360p = new InputStream($input, 'live', SelectionMode::AUTO);
$inputStream360p->setPosition(0);
$stream360p = new Stream($videoConfig360p, array($inputStream360p));
$stream360p = $apiClient->encodings()->streams($encoding)->create($stream360p);

// CREATE MUXING FOR 360p
$encodingOutput360p = new EncodingOutput($output);
$encodingOutput360p->setOutputPath($gcs_prefix . 'video/360p_dash');
$encodingOutput360p->setAcl(array(new Acl(AclPermission::ACL_PUBLIC_READ)));
$muxingStream360p = new MuxingStream();
$muxingStream360p->setStreamId($stream360p->getId());
$fmp4Muxing360p = new FMP4Muxing();
$fmp4Muxing360p->setInitSegmentName('init.mp4');
$fmp4Muxing360p->setSegmentLength(1);
$fmp4Muxing360p->setSegmentNaming('segment_%number%.m4s');
$fmp4Muxing360p->setOutputs(array($encodingOutput360p));
$fmp4Muxing360p->setStreams(array($muxingStream360p));
$fmp4Muxing360p = $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->create($fmp4Muxing360p);

// CREATE VIDEO STREAM FOR 240p
$videoConfig240p = new H264VideoCodecConfiguration('StreamDemo240p', H264Profile::HIGH, 400000, 30.0);
$videoConfig240p->setDescription('StreamDemo240p');
$videoConfig240p->setWidth(426);
$videoConfig240p->setHeight(240);
$videoConfig240p = $apiClient->codecConfigurations()->videoH264()->create($videoConfig240p);
$inputStream240p = new InputStream($input, 'live', SelectionMode::AUTO);
$inputStream240p->setPosition(0);
$stream240p = new Stream($videoConfig240p, array($inputStream240p));
$stream240p = $apiClient->encodings()->streams($encoding)->create($stream240p);

// CREATE MUXING FOR 240p
$encodingOutput240p = new EncodingOutput($output);
$encodingOutput240p->setOutputPath($gcs_prefix . 'video/240p_dash');
$encodingOutput240p->setAcl(array(new Acl(AclPermission::ACL_PUBLIC_READ)));
$muxingStream240p = new MuxingStream();
$muxingStream240p->setStreamId($stream240p->getId());
$fmp4Muxing240p = new FMP4Muxing();
$fmp4Muxing240p->setInitSegmentName('init.mp4');
$fmp4Muxing240p->setSegmentLength(1);
$fmp4Muxing240p->setSegmentNaming('segment_%number%.m4s');
$fmp4Muxing240p->setOutputs(array($encodingOutput240p));
$fmp4Muxing240p->setStreams(array($muxingStream240p));
$fmp4Muxing240p = $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->create($fmp4Muxing240p);

// CREATE AUDIO STREAM
$audioConfig48000 = new AACAudioCodecConfiguration('StreamDemoAAC48000', 128000, 48000);
$audioConfig48000->setDescription('StreamDemoAAC48000');
$audioConfig48000 = $apiClient->codecConfigurations()->audioAAC()->create($audioConfig48000);
$inputStreamAAC48000 = new InputStream($input, '/', SelectionMode::AUTO);
$inputStreamAAC48000->setPosition(1);
$streamAAC48000 = new Stream($audioConfig48000, array($inputStreamAAC48000));
$streamAAC48000 = $apiClient->encodings()->streams($encoding)->create($streamAAC48000);

// CREATE MUXING FOR AUDIO
$encodingOutputAAC48000 = new EncodingOutput($output);
$encodingOutputAAC48000->setOutputPath($gcs_prefix . 'audio/128kbps_dash');
$encodingOutputAAC48000->setAcl(array(new Acl(AclPermission::ACL_PUBLIC_READ)));
$muxingStreamAAC48000 = new MuxingStream();
$muxingStreamAAC48000->setStreamId($streamAAC48000->getId());
$fmp4MuxingAAC48000 = new FMP4Muxing();
$fmp4MuxingAAC48000->setInitSegmentName('init.mp4');
$fmp4MuxingAAC48000->setSegmentLength(1);
$fmp4MuxingAAC48000->setSegmentNaming('segment_%number%.m4s');
$fmp4MuxingAAC48000->setOutputs(array($encodingOutputAAC48000));
$fmp4MuxingAAC48000->setStreams(array($muxingStreamAAC48000));
$fmp4MuxingAAC48000 = $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->create($fmp4MuxingAAC48000);

// CREATE DASH MANIFEST
$manifestOutput = new EncodingOutput($output);
$manifestOutput->setOutputPath($gcs_prefix);
$manifestOutput->setAcl(array(new Acl(AclPermission::ACL_PUBLIC_READ)));

$dashManifest = new DashManifest();
$dashManifest->setName("stream.mpd");
$dashManifest->setManifestName("stream.mpd");
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
$audioRepresentation->setEncodingId($encoding->getId());
$audioRepresentation->setMuxingId($fmp4MuxingAAC48000->getId());
$audioRepresentation->setSegmentPath('audio/128kbps_dash');
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $period, $audioAdaptationSet, $audioRepresentation);

$videoRepresentation_1080p = new DashRepresentation();
$videoRepresentation_1080p->setType(DashMuxingType::TYPE_TEMPLATE);
$videoRepresentation_1080p->setEncodingId($encoding->getId());
$videoRepresentation_1080p->setMuxingId($fmp4Muxing1080p->getId());
$videoRepresentation_1080p->setSegmentPath('video/1080p_dash');
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $period, $videoAdaptationSet, $videoRepresentation_1080p);

$videoRepresentation_720p = new DashRepresentation();
$videoRepresentation_720p->setType(DashMuxingType::TYPE_TEMPLATE);
$videoRepresentation_720p->setEncodingId($encoding->getId());
$videoRepresentation_720p->setMuxingId($fmp4Muxing720p->getId());
$videoRepresentation_720p->setSegmentPath('video/720p_dash');
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $period, $videoAdaptationSet, $videoRepresentation_720p);

$videoRepresentation_480p = new DashRepresentation();
$videoRepresentation_480p->setType(DashMuxingType::TYPE_TEMPLATE);
$videoRepresentation_480p->setEncodingId($encoding->getId());
$videoRepresentation_480p->setMuxingId($fmp4Muxing480p->getId());
$videoRepresentation_480p->setSegmentPath('video/480p_dash');
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $period, $videoAdaptationSet, $videoRepresentation_480p);

$videoRepresentation_360p = new DashRepresentation();
$videoRepresentation_360p->setType(DashMuxingType::TYPE_TEMPLATE);
$videoRepresentation_360p->setEncodingId($encoding->getId());
$videoRepresentation_360p->setMuxingId($fmp4Muxing360p->getId());
$videoRepresentation_360p->setSegmentPath('video/360p_dash');
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $period, $videoAdaptationSet, $videoRepresentation_360p);

$videoRepresentation_240p = new DashRepresentation();
$videoRepresentation_240p->setType(DashMuxingType::TYPE_TEMPLATE);
$videoRepresentation_240p->setEncodingId($encoding->getId());
$videoRepresentation_240p->setMuxingId($fmp4Muxing240p->getId());
$videoRepresentation_240p->setSegmentPath('video/240p_dash');
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $period, $videoAdaptationSet, $videoRepresentation_240p);

// START LIVE STREAM
$liveDashManifest = new LiveDashManifest();
$liveDashManifest->setManifestId($dashManifest->getId());
$liveDashManifest->setLiveEdgeOffset(60);

$startLiveEncodingRequest = new \Bitmovin\api\model\encodings\StartLiveEncodingRequest();
$startLiveEncodingRequest->setStreamKey($stream_key);
$startLiveEncodingRequest->setDashManifests(array($liveDashManifest));

$apiClient->encodings()->startLivestreamWithManifests($encoding, $startLiveEncodingRequest);

// WAIT UNTIL LIVE STREAM IS RUNNING
$status = '';
do
{
    sleep(1);
    $status = $apiClient->encodings()->status($encoding)->getStatus();
}
while ($status != Status::ERROR && $status != Status::RUNNING);

// WAIT UNTIL LIVE STREAM DATA ARE AVAILABLE
$liveEncodingDetails = null;
do
{
    try
    {
        $liveEncodingDetails = $apiClient->encodings()->getLivestreamDetails($encoding);
    }
    catch(BitmovinException $exception)
    {
        if ($exception->getCode() != 400)
        {
            print 'Got unexpected exception with code ' . strval($exception->getCode()) . ': ' . $exception->getMessage();
            throw $exception;
        }
        sleep(1);
    }
}
while ($liveEncodingDetails == null);

print 'RTMP Url: rtmp://' . $liveEncodingDetails->getEncoderIp() . '/live' . "\n";
print 'Stream-Key: ' . $liveEncodingDetails->getStreamKey() . "\n";

$encodingInformation = array(
    'ENCODING_ID' => $encoding->getId(),
    'AUDIO_STREAM_ID' => $streamAAC48000->getId(),
    '1080_STREAM_ID' => $stream1080p->getId(),
    '720_STREAM_ID' => $stream720p->getId(),
    '480_STREAM_ID' => $stream480p->getId(),
    '360_STREAM_ID' => $stream360p->getId(),
    '240_STREAM_ID' => $stream240p->getId(),
    'FMP4_AUDIO_MUXING_ID' => $fmp4MuxingAAC48000->getId(),
    'FMP4_1080_MUXING_ID' => $fmp4Muxing1080p->getId(),
    'FMP4_720_MUXING_ID' => $fmp4Muxing720p->getId(),
    'FMP4_480_MUXING_ID' => $fmp4Muxing480p->getId(),
    'FMP4_360_MUXING_ID' => $fmp4Muxing360p->getId(),
    'FMP4_240_MUXING_ID' => $fmp4Muxing240p->getId(),
);

file_put_contents('encodingInformation.json', json_encode($encodingInformation));

print 'Saved encoding information to "encodingInformation.json" - You can now start to ingest and creating your Live-to-VoD manifests';
