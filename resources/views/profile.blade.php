<section class="config">
    <section class="configSect">
        <div class="profile">
            <p style="margin-bottom: unset;" class="confTitle">Settings</p>

            <div class="image"></div>

            <div class="side">
                <p style="margin-bottom: unset;" class="name"> logged in User</p>
                <p style="margin-bottom: unset;" class="pStatus">Online</p>
            </div>

            <button class="changePic">Change Profile Picture</button>
            <button class="edit">Edit Profile Info</button>
        </div>
    </section>

    <section class="configSect second">

        <!-- PROFILE INFO SECTION -->
        <p style="margin-bottom: unset;" class="confTitle">Your Info</p>

        @include('botinfo')

        <!-- NOTIFICATIONS SECTION -->
        <p style="margin-bottom: unset;" class="confTitle">Notifications</p>

        <div class="optionWrapper deskNotif">
            <input type="checkbox" id="deskNotif" class="toggleTracer" checked="">

            <label class="check deskNotif" for="deskNotif">
                <div class="tracer"></div>
            </label>
            <p>Enable Desktop Notifications</p>
        </div>

        <div class="optionWrapper showSName">
            <input type="checkbox" id="showSName" class="toggleTracer">

            <label class="check" for="showSName">
                <div class="tracer"></div>
            </label>
            <p>Show Sender Name</p>
        </div>

        <div class="optionWrapper showPreview">
            <input type="checkbox" id="showPreview" class="toggleTracer">

            <label class="check" for="showPreview">
                <div class="tracer"></div>
            </label>
            <p>Show Message Preview</p>
        </div>

        <div class="optionWrapper playSounds">
            <input type="checkbox" id="playSounds" class="toggleTracer">

            <label class="check" for="playSounds">
                <div class="tracer"></div>
            </label>
            <p>Play Sounds</p>
        </div>


        <p style="margin-bottom: unset;" class="confTitle">Other Settings</p>

        <div class="optionWrapper">
            <input type="checkbox" id="checkNight" class="toggleTracer">

            <label class="check DarkThemeTrigger" for="checkNight">
                <div class="tracer"></div>
            </label>
            <p>Dark Theme</p>
        </div>

    </section>
</section>
