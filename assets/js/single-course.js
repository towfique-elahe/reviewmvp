// description see more button js
document.addEventListener("DOMContentLoaded", function () {
  var desc = document.getElementById("course-desc");
  var btn = document.querySelector(".toggle-desc-btn");
  if (!desc || !btn) return;

  // If content doesn't overflow (i.e., not truncated), hide the button
  // Temporarily force clamp to check overflow
  var isOverflowing = desc.scrollHeight > desc.clientHeight + 1; // +1 buffer
  if (!isOverflowing) {
    btn.classList.add("is-hidden");
    return;
  }

  btn.addEventListener("click", function () {
    var expanded = btn.getAttribute("aria-expanded") === "true";
    btn.setAttribute("aria-expanded", String(!expanded));
    desc.classList.toggle("is-expanded", !expanded);

    var label = btn.querySelector(".toggle-desc-text");
    if (!expanded) {
      label.textContent = "Show less";
    } else {
      label.textContent = "Show more";
    }
  });
});

// review load more button js
document.addEventListener("DOMContentLoaded", function () {
  const loadMoreBtn = document.querySelector(".load-more-btn");
  const batchSize = 3; // number of reviews to show each click

  loadMoreBtn.addEventListener("click", function (e) {
    e.preventDefault();

    // Get a fresh list of hidden reviews each time
    const hiddenReviews = document.querySelectorAll(".hidden-review");

    // Show next batch
    for (let i = 0; i < batchSize && i < hiddenReviews.length; i++) {
      hiddenReviews[i].classList.remove("hidden-review");
    }

    // If no hidden reviews left, hide the button
    if (document.querySelectorAll(".hidden-review").length === 0) {
      loadMoreBtn.style.display = "none";
    }
  });
});
