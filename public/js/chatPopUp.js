function chatInit(selector) {
    document.addEventListener('DOMContentLoaded', () => {
        if (!window.LIVE_CHAT_UI) {
            let chat = document.querySelector(selector);
            let toggles = chat.querySelectorAll('.toggle')
            let close = chat.querySelector('.close')

            window.setTimeout(() => {
                chat.classList.add('is-active')
            }, 1000)

            toggles.forEach( toggle => {
                toggle.addEventListener('click', () => {
                    chat.classList.add('is-active')
                })
            })

            close.addEventListener('click', () => {
                chat.classList.remove('is-active')
            })

            document.onkeydown = function(evt) {
                evt = evt || window.event;
                var isEscape = false;
                if ("key" in evt) {
                    isEscape = (evt.key === "Escape" || evt.key === "Esc");
                } else {
                    isEscape = (evt.keyCode === 27);
                }
                if (isEscape) {
                    chat.classList.remove('is-active')
                }
            };

            window.LIVE_CHAT_UI = true
        }
    })
}

chatInit('#chat-app')
