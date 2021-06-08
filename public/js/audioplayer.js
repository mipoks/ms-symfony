/*
	AUTHOR: Osvaldas Valutis, www.osvaldas.info
*/
(function ($, window, document, undefined) {

    var audios = document.getElementsByClassName('audioplayer-playpause');
    var currentAudio;
    var currentPlayer;
    var theCurrentAudio;

    function playNextAudio() {
        console.log("try to play next audio")
        for (let i = 0; i < audios.length - 1; i++) {
            if (currentAudio && currentAudio == audios[i]) {
                audios[i + 1].click();
                break;
            }
        }
    }


    var isTouch = 'ontouchstart' in window,
        eStart = isTouch ? 'touchstart' : 'mousedown',
        eMove = isTouch ? 'touchmove' : 'mousemove',
        eEnd = isTouch ? 'touchend' : 'mouseup',
        eCancel = isTouch ? 'touchcancel' : 'mouseup',
        secondsToTime = function (secs) {
            var hours = Math.floor(secs / 3600),
                minutes = Math.floor(secs % 3600 / 60),
                seconds = Math.ceil(secs % 3600 % 60);
            return (hours == 0 ? '' : hours > 0 && hours.toString().length < 2 ? '0' + hours + ':' : hours + ':') + (minutes.toString().length < 2 ? '0' + minutes : minutes) + ':' + (seconds.toString().length < 2 ? '0' + seconds : seconds);
        },
        canPlayType = function (file) {
            var audioElement = document.createElement('audio');
            return !!(audioElement.canPlayType && audioElement.canPlayType('audio/' + file.split('.').pop().toLowerCase() + ';').replace(/no/, ''));
        };

    $.fn.audioPlayer = function (params) {
        var params = $.extend({
                classPrefix: 'audioplayer',
                strPlay: '',
                strPause: '',
                strVolume: ''
            }, params),
            cssClass = {},
            cssClassSub = {
                playPause: 'playpause',
                playing: 'playing',
                time: 'time',
                timeCurrent: 'time-current',
                timeDuration: 'time-duration',
                bar: 'bar',
                barLoaded: 'bar-loaded',
                barPlayed: 'bar-played',
                volume: 'volume',
                volumeButton: 'volume-button',
                addSongButton: 'add-song',
                volumeAdjust: 'volume-adjust',
                noVolume: 'novolume',
                mute: 'mute',
                mini: 'mini',
                tickmark: 'tick-container'
            };

        for (var subName in cssClassSub)
            cssClass[subName] = params.classPrefix + '-' + cssClassSub[subName];

        this.each(function () {
            if ($(this).prop('tagName').toLowerCase() != 'audio')
                return false;

            var $this = $(this),

                audioId = $this.attr('id'),
                origUrl = $this.attr('orig'),
                csrf = $this.attr('_csrf'),
                // csrf = "thisistempcsrf",
                audioFile = $this.attr('src'),
                songName = $this.attr('name'),
                isOwned = $this.attr('own'),
                isAutoPlay = $this.get(0).getAttribute('autoplay'),
                isAutoPlay = isAutoPlay === '' || isAutoPlay === 'autoplay' ? true : false,
                isLoop = $this.get(0).getAttribute('loop'),
                isLoop = isLoop === '' || isLoop === 'loop' ? true : false,
                isSupport = true;


            if (typeof audioFile === 'undefined') {
                $this.find('source').each(function () {
                    audioFile = $(this).attr('src');
                    if (typeof audioFile !== 'undefined' && canPlayType(audioFile)) {
                        isSupport = true;
                        return false;
                    }
                });
            } else if (canPlayType(audioFile)) isSupport = true;

            var thePlayer = $('<div class="' + params.classPrefix + '">' + (isSupport ? $('<div>').append($this.eq(0).clone()).html() : '<embed src="' + audioFile + '" width="0" height="0" volume="100" autostart="' + isAutoPlay.toString() + '" loop="' + isLoop.toString() + '" />') + '<div class="' + cssClass.playPause + '" title="' + params.strPlay + '"><a href="#">' + params.strPlay + '</a></div></div>'),
                theAudio = isSupport ? thePlayer.find('audio') : thePlayer.find('embed'),
                theAudio = theAudio.get(0);

            if (isSupport) {
                thePlayer.find('audio').css({
                    'width': 0,
                    'height': 0,
                    'visibility': 'hidden'
                });
                thePlayer.append('<div class="' + cssClass.time + ' ' + cssClass.timeCurrent + '"></div>' +
                    '<div class="bar-name-container">' +
                    '<div class="song-name marquee"><span>' + songName + '</span></div>' +
                    '<div class="' + cssClass.bar + '">' +
                    '<div class="' + cssClass.barLoaded + '"></div>' +
                    '<div class="' + cssClass.barPlayed + '"></div></div></div>' +
                    '<div class="' + cssClass.time + ' ' + cssClass.timeDuration + '"></div>' +
                    '<div class="' + cssClass.volume + '">' +
                    '<div class="' + cssClass.volumeButton + '" title="' + params.strVolume + '">' +
                    '<a href="#">' + params.strVolume + '</a></div>' +
                    '<div class="' + (isOwned == "true" ? cssClass.tickmark : cssClass.addSongButton) + '"><a href="#"></a></div>' +
                    '<div class="' + cssClass.volumeAdjust + '"><div><div></div></div></div></div>');

                var theBar = thePlayer.find('.' + cssClass.bar),
                    barPlayed = thePlayer.find('.' + cssClass.barPlayed),
                    barLoaded = thePlayer.find('.' + cssClass.barLoaded),
                    timeCurrent = thePlayer.find('.' + cssClass.timeCurrent),
                    timeDuration = thePlayer.find('.' + cssClass.timeDuration),
                    volumeButton = thePlayer.find('.' + cssClass.volumeButton),
                    addSongButton = thePlayer.find('.' + cssClass.addSongButton),
                    tickMarkButton = thePlayer.find('.' + cssClass.tickmark),
                    volumeAdjuster = thePlayer.find('.' + cssClass.volumeAdjust + ' > div'),
                    volumeDefault = 0,
                    adjustCurrentTime = function (e) {
                        theRealEvent = isTouch ? e.originalEvent.touches[0] : e;
                        theAudio.currentTime = Math.round((theAudio.duration * (theRealEvent.pageX - theBar.offset().left)) / theBar.width());
                    },
                    adjustVolume = function (e) {
                        theRealEvent = isTouch ? e.originalEvent.touches[0] : e;
                        theAudio.volume = Math.abs((theRealEvent.pageX - volumeAdjuster.offset().left) / volumeAdjuster.width());
                    },
                    updateLoadBar = setInterval(function () {
                        if (theAudio.buffered.length > 0) {
                            barLoaded.width((theAudio.buffered.end(0) / theAudio.duration) * 100 + '%');
                            if (theAudio.buffered.end(0) >= theAudio.duration)
                                clearInterval(updateLoadBar);
                        }
                    }, 100);

                var volumeTestDefault = theAudio.volume,
                    volumeTestValue = theAudio.volume = 0.111;
                if (Math.round(theAudio.volume * 1000) / 1000 == volumeTestValue) theAudio.volume = volumeTestDefault;
                else thePlayer.addClass(cssClass.noVolume);

                timeDuration.html('&hellip;');
                timeCurrent.text(secondsToTime(0));

                theAudio.addEventListener('loadeddata', function () {
                    timeDuration.text(secondsToTime(theAudio.duration));
                    volumeAdjuster.find('div').width(theAudio.volume * 100 + '%');
                    volumeDefault = theAudio.volume;
                });

                theAudio.addEventListener('timeupdate', function () {
                    timeCurrent.text(secondsToTime(theAudio.currentTime));
                    barPlayed.width((theAudio.currentTime / theAudio.duration) * 100 + '%');
                });

                theAudio.addEventListener('volumechange', function () {
                    volumeAdjuster.find('div').width(theAudio.volume * 100 + '%');
                    if (theAudio.volume > 0 && thePlayer.hasClass(cssClass.mute)) thePlayer.removeClass(cssClass.mute);
                    if (theAudio.volume <= 0 && !thePlayer.hasClass(cssClass.mute)) thePlayer.addClass(cssClass.mute);
                });

                theAudio.addEventListener('ended', function () {
                    playNextAudio();
                    thePlayer.removeClass(cssClass.playing);
                });

                theBar.on(eStart, function (e) {
                    adjustCurrentTime(e);
                    theBar.on(eMove, function (e) {
                        adjustCurrentTime(e);
                    });
                })
                    .on(eCancel, function () {
                        theBar.unbind(eMove);
                    });

                volumeButton.on('click', function () {
                    if (thePlayer.hasClass(cssClass.mute)) {
                        thePlayer.removeClass(cssClass.mute);
                        theAudio.volume = volumeDefault;
                    } else {
                        thePlayer.addClass(cssClass.mute);
                        volumeDefault = theAudio.volume;
                        theAudio.volume = 0;
                    }
                    return false;
                });

                function addDeleteAction() {
                    function getXmlHttp() {
                        var xmlhttp;
                        try {
                            xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
                        } catch (e) {
                            try {
                                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                            } catch (E) {
                                xmlhttp = false;
                            }
                        }
                        if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
                            xmlhttp = new XMLHttpRequest();
                        }
                        return xmlhttp;
                    }

                    // var xmlhttp = getXmlHttp();
                    // xmlhttp.open('post', 'me', true);
                    // xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
                    //
                    // xmlhttp.onreadystatechange = function () {
                    //     if (xmlhttp.readyState == 4) {
                    //         let elemAS = nice.querySelector(".audioplayer-add-song");
                    //         let elemTM = nice.querySelector(".audioplayer-tick-container");
                    //         if (xmlhttp.status == 200) {
                    //             $.notify("Трэк будет добавлен в Ваш плейлист", "success");
                    //             if (elemAS != null) {
                    //                 elemAS.classList.add(cssClass.tickmark);
                    //                 elemAS.classList.remove(cssClass.addSongButton);
                    //             } else {
                    //                 elemTM.classList.add(cssClass.addSongButton);
                    //                 elemTM.classList.remove(cssClass.tickmark);
                    //             }
                    //         }
                    //         if (xmlhttp.status == 401) {
                    //             $.notify("Войдите, чтобы сохранять музыку", "warn");
                    //         }
                    //         if (xmlhttp.status == 500) {
                    //             $.notify("Ошибка. Попробуйте позднее", "warn");
                    //         }
                    //         if (xmlhttp.status == 202) {
                    //             $.notify("Трэк будет удален из Вашего плейлист", "success");
                    //             if (elemTM != null) {
                    //                 elemTM.classList.add(cssClass.addSongButton);
                    //                 elemTM.classList.remove(cssClass.tickmark);
                    //             } else {
                    //                 elemAS.classList.add(cssClass.tickmark);
                    //                 elemAS.classList.remove(cssClass.addSongButton);
                    //             }
                    //         }
                    //     }
                    // };
                    //console.log(audioId + " " + origUrl + " " + songName + " " + encodeURIComponent(songName));
                    // xmlhttp.send("orig=" + origUrl + "&id=" + audioId + "&name=" + encodeURIComponent(songName)
                    // + "&_csrf=" + csrf);
                    $.notify("Подождите. Жду ответа от сервера", "success");

                    let tempId = "";
                    for (let i = 0; i < audioId.length; i++) {
                        if (audioId[i] >= '0' && audioId[i] <= '9') {
                            tempId += audioId[i];
                        }
                    }
                    audioId = tempId;
                    console.log(audioId + "Мы здесь");

                    console.log("ffff me" + audioId);
                    let urlForPost = "/me/" + audioId;
                    $.ajax({
                        url:urlForPost,
                        headers: {"X-CSRF-TOKEN": csrf},
                        type:"POST",
                        data: JSON.stringify({ "orig": origUrl, "id": audioId.replace("&nbsp;",''), "name": encodeURIComponent(songName)}),
                        contentType:"application/json; charset=utf-8",
                        dataType:"json",
                        complete: function(xhr, textStatus) {
                            let elemAS = nice.querySelector(".audioplayer-add-song");
                            let elemTM = nice.querySelector(".audioplayer-tick-container");

                            console.log(xhr.status);
                            console.log(xhr);
                            console.log("/me/" + audioId);
                            switch (xhr.status) {
                                case 202:
                                    $.notify("Трэк будет добавлен в Ваш плейлист", "success");
                                    if (elemAS != null) {
                                        elemAS.classList.add(cssClass.tickmark);
                                        elemAS.classList.remove(cssClass.addSongButton);
                                    } else {
                                        elemTM.classList.add(cssClass.addSongButton);
                                        elemTM.classList.remove(cssClass.tickmark);
                                    }
                                    break;
                                case 203:
                                    $.notify("Трэк будет удален из Вашего плейлист", "success");
                                    if (elemTM != null) {
                                        elemTM.classList.add(cssClass.addSongButton);
                                        elemTM.classList.remove(cssClass.tickmark);
                                    } else {
                                        elemAS.classList.add(cssClass.tickmark);
                                        elemAS.classList.remove(cssClass.addSongButton);
                                    }
                                    break;
                                case 200:
                                    $.notify("Войдите, чтобы сохранять музыку", "warn");
                                    break;
                            }
                        }
                    });
                    console.log("AJAX был отправлен вроде");
                    // Отправляет данные при помощи POST запроса
                    // var posting = $.post( "/me", , "json" );

                    // Помещаем результат внутрь div элемента


                    return false;
                }

                //console.log(this);
                let nice = this.parentElement;
                //console.log(nice);

                addSongButton.on('click', addDeleteAction);

                tickMarkButton.on('click', addDeleteAction);

                volumeAdjuster.on(eStart, function (e) {
                    adjustVolume(e);
                    volumeAdjuster.on(eMove, function (e) {
                        adjustVolume(e);
                    });
                })
                    .on(eCancel, function () {
                        volumeAdjuster.unbind(eMove);
                    });
            } else thePlayer.addClass(cssClass.mini);

            if (isAutoPlay) thePlayer.addClass(cssClass.playing);

            thePlayer.find('.' + cssClass.playPause).on('click', function () {
                if (thePlayer.hasClass(cssClass.playing)) {
                    $(this).attr('title', params.strPlay).find('a').html(params.strPlay);
                    thePlayer.removeClass(cssClass.playing);
                    isSupport ? theAudio.pause() : theAudio.Stop();
                } else {
                    $(this).attr('title', params.strPause).find('a').html(params.strPause);
                    thePlayer.addClass(cssClass.playing);
                    currentAudio = this;
                    if (currentPlayer && currentPlayer != thePlayer) {
                        currentPlayer.removeClass(cssClass.playing);
                        isSupport ? theCurrentAudio.pause() : theCurrentAudio.Stop();
                    }
                    currentPlayer = thePlayer;
                    theCurrentAudio = theAudio;
                    isSupport ? theAudio.play() : theAudio.Play();
                }
                return false;
            });

            $this.replaceWith(thePlayer);
        });
        return this;
    };
})(jQuery, window, document);
