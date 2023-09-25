class AnnouncementBar {
    constructor() {
        this.localStorageKeyName = "announcement_bar_is_hidden";
        this.notice = null;
        this.identifier = null;
        this.isHidden = this.isHidden.bind(this);
        this.removeKey = this.removeKey.bind(this);
        this.noticeClose = this.noticeClose.bind(this);
    }

    init() {
        this.notice = document.querySelector(".announcement-bar");
        this.identifier = this.notice?.getAttribute("data-identifier");

        if (!this.notice) {
            console.error("Announcement Bar is not found.");
            return false;
        }

        if (!this.identifier) {
            console.error(
                "Announcement Bar is missing data-identifier attribute."
            );
            return false;
        }

        if (this.isHidden()) {
            this.hideNotice();
        } else {
            this.showNotice();
        }

        this.noticeClose();
    }

    isHidden() {
        return localStorage?.getItem(this.localStorageKeyName) === this.identifier;
    }

    hideNotice() {
        localStorage?.setItem(this.localStorageKeyName, this.identifier);
		this.notice.classList.add('announcement-bar--hidden');
        document.body.classList.remove("has-announcement-bar");
    }

    showNotice() {
        this.notice.classList.remove("announcement-bar--hidden");
    }

    removeKey() {
        localStorage.removeItem(this.localStorageKeyName);
    }

    noticeClose() {
        const close = this.notice.querySelector(".announcement-bar-close");

        if (!close) {
            return;
        }

        close.addEventListener("click", (e) => {
			e.preventDefault();
            this.hideNotice();
        });
    }
}

const announcementBar = new AnnouncementBar();

document.addEventListener("DOMContentLoaded", () => {
    announcementBar.init();
});
