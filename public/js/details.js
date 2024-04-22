const summaries = document.querySelectorAll("summary");

summaries.forEach((summary) => {
    summary.addEventListener("click", closeOpenedDetails);
});

function closeOpenedDetails() {
    summaries.forEach((summary) => {
        let detail = summary.parentNode;
        if (detail != this.parentNode) {
            detail.removeAttribute("open");
        }
    });
}
