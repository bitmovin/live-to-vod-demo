<!DOCTYPE html>
<!--
*
* Copyright (C) 2017, Bitmovin Inc, All Rights Reserved
*
* Created on: 2015-07-25 11:35:04
* Author:     Bitmovin inc <sales@bitmovin.com>
*
* This source code and its use and distribution, is subject to the terms
* and conditions of the applicable license agreement.
*
-->
<html lang="en">
<head>
    <title>Live To VoD</title>
    <link rel="icon" type="image/png" href="images/bit-fav.png">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/main_style.css">
    <link rel="stylesheet" href="css/font.css">
    <link rel="stylesheet" href="css/jquery-ui.css">
    <link rel="stylesheet" href="css/style.css">
    <!-- Bitmovin player -->
    <script type="text/javascript" src="js/bitmovinplayer.js"></script>
    <script type="text/javascript" src="player-ui/bitmovinplayer-ui.min.js"></script>
    <link rel="stylesheet" href="player-ui/bitmovinplayer-ui.min.css">
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">

    <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/bootstrap-lightbox.js"></script>
    <script src="js/jquery-ui.js"></script>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script type="text/javascript" src="js/liveToVodFunctions.js"></script>
    <style>
        .vods-list {
            margin: 0;
        }
    </style>
</head>
<body>
<div id="wrapper">
    <div id="banner">
        <div class="logo">
            <a href="../">
                <img src="images/bitmovin-logo.png">
            </a>
        </div>
        <div class="title">
            <img src="images/logos/live-streaming.png"/>
        </div>
        <div class="clear"></div>
    </div>
    <div id="main">
        <div class="row">
            <div class="col-lg-6 main-left">
                <div class="row">
                    <h1>Live-to-VoD</h1>
                    <ul>
                        <li>No additional encoding costs</li>
                        <li>VoD immediately available after live event</li>
                        <li>Get your VoD clips online during live events</li>
                        <li>Reuse content for e.g. social media marketing</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-6 main-right">
                <div class="row">
                    <div class="col-lg-12 live-to-vod-image">
                        <img class="bitmovin-lightbox" src="images/live-stream-to-vod.png"  data-img-url="images/live-stream-to-vod.png">
                    </div>
                </div>
            </div>
        </div>
		<hr />
        <div class="row live-vod">
            <div class="col-lg-6 main-left">
                <h1>Live</h1>
                <div class="player-wrapper">
                    <div id="player-live"></div>
                </div>
                <br>
                <div id="timeline-container" class="timeline-container">
                </div>
                <br>
                <div class="text-center">
                    <div class="btn-group" role="group" aria-label="...">
                        <button type="button" id="15secsButton" class="btn btn-bitmovin" data-load-text="<i class='fa fa-circle-o-notch fa-spin'></i> Generating manifest" data-default-text="Last 15 secs to VOD">Last 15 secs to VOD</button>
                        <button type="button" id="30secsButton" class="btn btn-bitmovin" data-load-text="<i class='fa fa-circle-o-notch fa-spin'></i> Generating manifest" data-default-text="Last 30 secs to VOD">Last 30 secs to VOD</button>
                        <button type="button" id="timelineButton" class="btn btn-bitmovin" data-load-text="<i class='fa fa-circle-o-notch fa-spin'></i> Generating manifest" data-default-text="Generate from Selection">Generate from Selection</button>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 main-right">
                <h1>VoD</h1>
                <div class="player-wrapper">
                    <div id="player-vod"></div>
                </div>
                <br>
                <div class="col-lg-12">
                    <ul class="vods-list">
                    </ul>
                </div>
            </div>
        </div>
        <script>
            $(document).ready(function () {
                setupPlayer();

                $("#timelineButton").click(function() {
                    var button = $(this);
                    button.html(button.data('load-text'));
                    button.attr("disabled", true);
                    generateVODFromTimeline(button);
                });

                $("#15secsButton").click(function () {
                    var button = $(this);
                    button.html(button.data('load-text'));
                    button.attr("disabled", true);
                    generateTimedClip(15, button);
                });

                $("#30secsButton").click(function () {
                    var button = $(this);
                    button.html(button.data('load-text'));
                    button.attr("disabled", true);
                    generateTimedClip(30, button);
                });

                $("#timeline-selector").resizable({
                    containment: "#timeline-container",
                    grid: 160
                }).draggable({
                    containment: "#timeline-container",
                    axis: "x",
                    grid: [160, 0]
                });
            });
        </script>
    </div>
</div>
</body>
</html>
