<div class="rightPanel">

    <div class="topBar">
        <div class="rightSide">
            <button class="tbButton search">
                <i class="material-icons"></i>
            </button>
            <button class="tbButton otherOptions">
                <i class="material-icons">more_vert</i>
            </button>
        </div>
        <div id="top">

        </div>
    </div>

    <div class="convHistory userBg" id="scrollBar">
        <!-- CONVERSATION GOES HERE! -->


    </div>

    <div class="replyBar">
        <button class="attach">
            <i class="material-icons d45">attach_file</i>
        </button>

        <input type="text" class="replyMessage" placeholder="Type your message...">

        <div class="emojiBar" style="display: none;">
            <div class="emoticonType">
                <button id="panelEmoji">Emoji</button>
                <button id="panelStickers">Stickers</button>
                <button id="panelGIFs">GIFs</button>
            </div>


            <!-- Emoji panel -->
            <div class="emojiList">
                <button id="smileface" class="pick">
                </button>
                <button id="grinningface" class="pick"></button>
                <button id="tearjoyface" class="pick"></button>
                <button id="rofl" class="pick"></button>
                <button id="somface" class="pick"></button>
                <button id="swfface" class="pick"></button>
            </div>

            <!-- the best part of telegram ever: STICKERS!! -->
            <div class="stickerList">
                <button id="smileface" class="pick">
                </button>
                <button id="grinningface" class="pick"></button>
                <button id="tearjoyface" class="pick"></button>
            </div>
        </div>

        <div class="otherTools">
            <button class="toolButtons emoji">
                <i class="material-icons">face</i>
            </button>

            <button class="toolButtons audio">
                <i class="material-icons">mic</i>
            </button>
        </div>
    </div>
</div>
