document.addEventListener("DOMContentLoaded", function () {
  const buttons = document.querySelectorAll(".tab-button");
  const tabs = document.querySelectorAll(".tab-content");

  buttons.forEach((btn) => {
    btn.addEventListener("click", function () {
      // Remove active from all
      buttons.forEach((b) => b.classList.remove("active"));
      tabs.forEach((tab) => tab.classList.remove("active"));

      // Add active to selected
      this.classList.add("active");
      document.getElementById(this.dataset.tab).classList.add("active");
    });
  });
});
