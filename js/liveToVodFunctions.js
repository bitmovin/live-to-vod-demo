const playerKey = "ENTER-YOUR-PLAYER-KEY";

if (location.protocol === "file:") {
    document.getElementById("webserver-warning").style.display = "block";
}

var takeScreenshotsEveryNSegments = 5;

var currentPlayingSegment = 0;
var lastPlayedSegment = -1;
var currentPlayingTime = 0;

var segmentDuration = -1;
var screenshots = [];

var selectionStart = -1;
var selectionEnd = -1;

var playedSegments = 0;

var playerLive = null;
var playerVod = null;

function setupPlayer() {

    var settingsPanel = new bitmovin.playerui.SettingsPanel({
        components: [
            new bitmovin.playerui.SettingsPanelItem('Video Quality', new bitmovin.playerui.VideoQualitySelectBox()),
            new bitmovin.playerui.SettingsPanelItem('Speed', new bitmovin.playerui.PlaybackSpeedSelectBox()),
            new bitmovin.playerui.SettingsPanelItem('Audio Track', new bitmovin.playerui.AudioTrackSelectBox()),
            new bitmovin.playerui.SettingsPanelItem('Audio Quality', new bitmovin.playerui.AudioQualitySelectBox()),
            new bitmovin.playerui.SettingsPanelItem('Subtitles', new bitmovin.playerui.SubtitleSelectBox())
        ],
        hidden: true
    });

    var controlBarNoSeek = new bitmovin.playerui.ControlBar({
        components: [
            settingsPanel,
            new bitmovin.playerui.Container({
                components: [
                    new bitmovin.playerui.PlaybackTimeLabel({
                        timeLabelMode: bitmovin.playerui.PlaybackTimeLabelMode.CurrentTime,
                        hideInLivePlayback: true
                    }),
                    new bitmovin.playerui.PlaybackTimeLabel({
                        timeLabelMode: bitmovin.playerui.PlaybackTimeLabelMode.TotalTime,
                        cssClasses: ['text-right', 'full-width']
                    })
                ],
                cssClasses: ['controlbar-top']
            }),
            new bitmovin.playerui.Container({
                components: [
                    new bitmovin.playerui.PlaybackToggleButton(),
                    new bitmovin.playerui.VolumeToggleButton(),
                    new bitmovin.playerui.VolumeSlider(),
                    new bitmovin.playerui.Component({cssClass: 'spacer'}),
                    new bitmovin.playerui.CastToggleButton(),
                    new bitmovin.playerui.VRToggleButton(),
                    new bitmovin.playerui.SettingsToggleButton({settingsPanel: settingsPanel}),
                    new bitmovin.playerui.FullscreenToggleButton()
                ],
                cssClasses: ['controlbar-bottom']
            })
        ]
    });

    var controlBar = new bitmovin.playerui.ControlBar({
        components: [
            settingsPanel,
            new bitmovin.playerui.Container({
                components: [
                    new bitmovin.playerui.PlaybackTimeLabel({
                        timeLabelMode: bitmovin.playerui.PlaybackTimeLabelMode.CurrentTime,
                        hideInLivePlayback: true
                    }),
                    new bitmovin.playerui.SeekBar({label: new bitmovin.playerui.SeekBarLabel()}),
                    new bitmovin.playerui.PlaybackTimeLabel({
                        timeLabelMode: bitmovin.playerui.PlaybackTimeLabelMode.TotalTime,
                        cssClasses: ['text-right']
                    })
                ],
                cssClasses: ['controlbar-top']
            }),
            new bitmovin.playerui.Container({
                components: [
                    new bitmovin.playerui.PlaybackToggleButton(),
                    new bitmovin.playerui.VolumeToggleButton(),
                    new bitmovin.playerui.VolumeSlider(),
                    new bitmovin.playerui.Component({cssClass: 'spacer'}),
                    new bitmovin.playerui.CastToggleButton(),
                    new bitmovin.playerui.VRToggleButton(),
                    new bitmovin.playerui.SettingsToggleButton({settingsPanel: settingsPanel}),
                    new bitmovin.playerui.FullscreenToggleButton()
                ],
                cssClasses: ['controlbar-bottom']
            })
        ]
    });

    var uiNoWatermarkNoSeek = new bitmovin.playerui.UIContainer({
        components: [
            new bitmovin.playerui.SubtitleOverlay(),
            new bitmovin.playerui.BufferingOverlay(),
            new bitmovin.playerui.PlaybackToggleOverlay(),
            new bitmovin.playerui.CastStatusOverlay(),
            controlBarNoSeek,
            //new bitmovin.playerui.Watermark(),
            new bitmovin.playerui.TitleBar(),
            new bitmovin.playerui.RecommendationOverlay(),
            new bitmovin.playerui.ErrorMessageOverlay()
        ],
        cssClasses: ['ui-skin-modern'],
        hideDelay: -1
    });

    var uiNoWatermark = new bitmovin.playerui.UIContainer({
        components: [
            new bitmovin.playerui.SubtitleOverlay(),
            new bitmovin.playerui.BufferingOverlay(),
            new bitmovin.playerui.PlaybackToggleOverlay(),
            new bitmovin.playerui.CastStatusOverlay(),
            controlBar,
            //new bitmovin.playerui.Watermark(),
            new bitmovin.playerui.TitleBar(),
            new bitmovin.playerui.RecommendationOverlay(),
            new bitmovin.playerui.ErrorMessageOverlay()
        ],
        cssClasses: ['ui-skin-modern'],
        hideDelay: -1
    });

    playerLive = bitmovin.player("player-live");

    $.getJSON("getLiveManifest.php", function (manifests) {
        var confLive = {
            key: playerKey,
            source: {
                dash: manifests.DASH
            },
            tweaks: {
                max_buffer_level: 5
            },
            events: {
                onSegmentPlayback: onLiveSegmentPlayback
            },
            style: {
                ux: false
            }
        };

        var liveUiManager;
        playerLive.setup(confLive).then(function (value) {
            // Add the UI to the player
            liveUiManager = new bitmovin.playerui.UIManager(playerLive, uiNoWatermarkNoSeek);
            playerLive.play();

        }, function (reason) {
            console.log(reason);
            console.log("Error while creating bitmovin player instance");
        });

    });

    playerVod = bitmovin.player("player-vod");
    var confVod = {
        key: playerKey,
        source: {
            poster: 'images/poster.png'
        },
        tweaks: {
            max_buffer_level: 5
        },
        style: {
            ux: false
        }
    };

    var vodUiManager;
    playerVod.setup(confVod).then(function () {
        // Add the UI to the player
        vodUiManager = new bitmovin.playerui.UIManager(playerVod, uiNoWatermark);

    }, function (reason) {
        console.log(reason);
        console.log("Error while creating bitmovin player instance");
    });

}

function onLiveSegmentPlayback(url) {
    segmentDuration = url.duration;
    currentPlayingTime = url.playbackTime;
    currentPlayingSegment = parseInt(currentPlayingTime / segmentDuration);
    if (lastPlayedSegment !== currentPlayingSegment) {
        $("#currentSegment").val(currentPlayingSegment);
        if (playedSegments % takeScreenshotsEveryNSegments === 0) {
            var scrollToVeryRight = (((screenshots.length * 150) - $('#timeline-container').width()) - $('#timeline-container').scrollLeft()) < 2;
            addScreenshotToTimeline({
                segment: currentPlayingSegment,
                snapshot: playerLive.getSnapshot('image/jpeg', 0.8),
                timestamp: new Date(),
                index: screenshots.length
            });
            if (scrollToVeryRight) {
                $('#timeline-container').animate({scrollLeft: '+=1500'}, 500);
            }
        }
        playedSegments++;
        lastPlayedSegment = currentPlayingSegment;
    }
}

function loadVodPlayerWithManifest(manifests) {
    var playerVod = bitmovin.player("player-vod");
    var sourceUrl = {
        dash: manifests.DASH
    };
    playerVod.load(sourceUrl).then(function () {
        playerVod.play();
    }, function (reason) {
        console.log(reason);
        console.log("Error while creating bitmovin player instance");
    });
}
function runVoDManifest(start, end, button, img) {
    var url = "getVodManifest.php?start=" + start + "&end=" + end;
    $.getJSON(url, function (manifests) {
        img.data('manifests', manifests);
        loadVodPlayerWithManifest(manifests);
        button.html(button.data('default-text'));
        button.removeAttr('disabled');
    });
}

function addScreenshotToTimeline(screenshotObject) {
    screenshots.push(screenshotObject);
    const img = getScreenshotContainer(screenshotObject);
    img.appendTo('.timeline-container');
}

function getScreenshotContainer(screenshotObject) {
    const width = 150;
    const height = parseInt((width / screenshotObject.snapshot.width) * screenshotObject.snapshot.height);

    var imageContainer = '<img src="' + screenshotObject.snapshot.data + '" width="' + width + '" height="' + height + '">';
    if (screenshotObject.selected) {
        imageContainer = '<img class="selected" src="' + screenshotObject.snapshot.data + '" width="' + width + '" height="' + height + '">';
    }
    var img = $(imageContainer);
    img.data('segment', screenshotObject.segment);
    img.data('index', screenshotObject.index);
    img.click(timelineElementOnClick);
    return img;
}

function timelineElementOnClick() {
    const index = $(this).data('index');
    if (selectionStart === -1) {
        selectionStart = index;
    } else if (selectionEnd === -1) {
        selectionEnd = index;
    } else {
        if (index < selectionStart) {
            selectionStart = index;
        } else if (index > selectionEnd) {
            selectionEnd = index;
        } else if (index > selectionStart && index < selectionEnd) {
            selectionEnd = index;
        }
    }
    readrawTimeline();
}

function readrawTimeline() {
    if (selectionEnd === -1 && selectionStart !== -1) {
        const child = $('.timeline-container img:nth-of-type(' + (selectionStart + 1) + ')');
        child.addClass('selected');
    } else {
        for (var i = 0; i < screenshots.length; i++) {
            var selected = false;
            if (selectionStart !== -1 && selectionEnd !== -1) {
                selected = (i >= selectionStart && i <= selectionEnd);
            } else {
                selected = (i === selectionStart);
            }
            const child = $('.timeline-container img:nth-of-type(' + (i + 1) + ')');
            if (selected) {
                child.addClass('selected');
            } else {
                child.removeClass('selected');
            }
        }
    }
}

function generateTimedClip(pastNSeconds, button) {
    const endSegment = currentPlayingSegment;
    const startSegment = endSegment - parseInt((pastNSeconds) / segmentDuration);

    const timestamp = new Date();
    const startSegmentScreenshot = {
        segment: startSegment,
        snapshot: playerLive.getSnapshot('image/jpeg', 0.8),
        timestamp: new Date(timestamp.getTime() - pastNSeconds * 1000)
    };
    const endSegmentScreenshot = {
        segment: endSegment,
        snapshot: playerLive.getSnapshot('image/jpeg', 0.8),
        timestamp: timestamp
    };
    generateAndRunVOD(startSegmentScreenshot, endSegmentScreenshot, button);
}

function generateAndRunVOD(startSegment, endSegment, button) {

    const startSegmentNumber = startSegment.segment;
    const endSegmentNumber = endSegment.segment;
    var imageContainer = '<div class="imageContainer">' +
        '<img src="' + startSegment.snapshot.data + '">' +
        '</div>';
    var img = $(imageContainer);
    img.prependTo('.vods-list');
    img.click(function () {
        loadVodPlayerWithManifest($(this).data('manifests'));
    });
    runVoDManifest(startSegmentNumber, endSegmentNumber, button, img);
}

function generateVODFromTimeline(button) {
    const startSegment = screenshots[selectionStart];
    const endSegment = jQuery.extend(true, {}, screenshots[selectionEnd]);
    endSegment.segment = endSegment.segment + takeScreenshotsEveryNSegments;
    generateAndRunVOD(startSegment, endSegment, button);
    selectionEnd = -1;
    selectionStart = -1;
    readrawTimeline();
}
