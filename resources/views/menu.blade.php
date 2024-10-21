<section class="menuWrap">
    <div class="menu">
        <div class="me userBg">
            <div class="image"></div>

            <div class="myinfo">
                <p style="margin-bottom: unset;" class="name">{{ auth()->user()->name }}</p>
                <p style="margin-bottom: unset;" class="phone">{{  auth()->user()->phone }}</p>
            </div>

            <button class="cloud">
                <i class="material-icons">bookmark</i>
            </button>

            <button class="settings">
                <i class="material-icons">settings</i>
            </button>
        </div>
        <nav>
            <button class="ng">
                <i class="material-icons"></i>

                <span>New Group</span>
            </button>

            <button class="nc">
                <i class="material-icons"></i>

                <span>New Channel</span>
            </button>

            <button class="cn">
                <i class="material-icons"></i>

                <span>Contacts</span>
            </button>

            <button class="cl">
                <i class="material-icons"></i>

                <span>Calls History</span>
            </button>

            <a href="https://telegram.org/faq" target="_blank">
                <button class="faq">
                    <i class="material-icons"></i>

                    <span>FAQ and Support</span>
                </button>
            </a>

            <button class="lo">
                <a  href="{{ route('logout.post') }}"
                    onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                    <i class="material-icons"></i>
                    <span>Logout</span>
                </a>

            </button>


            <form id="logout-form" action="{{ route('logout.post') }}" method="POST" class="d-none">
                @csrf
            </form>
        </nav>

        <div class="info">
            <p>Telegram Web</p>
            <p>Ver 0.0.2 - <a href="https://en.wikipedia.org/wiki/Telegram_(messaging_service)">About</a></p>
            <p>Layout coded by: <a href="https://www.github.com/mayrinck">Mayrinck</a></p>
        </div>
    </div>
</section>
