(function () {
  const wrap = document.querySelector("#searchCourse");
  if (!wrap) return;

  const input = wrap.querySelector("#courseSearchInput");
  const popover = wrap.querySelector(".results-popover");
  const list = wrap.querySelector("#courseSearchList");
  const addMissing = wrap.querySelector(".add-missing");

  const addMissingUrl =
    window.courseSearchData?.addMissingUrl || "/add-missing-course/";
  const labels = window.courseSearchData?.labels || {
    course: "Course",
    category: "Category",
    noMatch: "Add missing course",
  };

  let activeIndex = -1;
  let currentResults = [];
  let timer;

  function open() {
    wrap.classList.add("is-open");
    wrap.setAttribute("aria-expanded", "true");
    popover.hidden = false;
  }

  function close() {
    wrap.classList.remove("is-open");
    wrap.setAttribute("aria-expanded", "false");
    popover.hidden = true;
    activeIndex = -1;
    input.setAttribute("aria-activedescendant", "");
  }

  function render(results) {
    currentResults = results;
    list.innerHTML = "";
    if (results.length) {
      addMissing.style.display = "none";
      results.forEach((r, i) => {
        const li = document.createElement("li");
        li.className = "result-item";
        li.id = `cs-opt-${i}`;
        li.setAttribute("role", "option");
        li.setAttribute("aria-selected", "false");
        li.innerHTML = `
          <a href="${r.url}">
            <div class="result-name">${r.name}</div>
            <div class="result-type">${labels[r.type] || ""}</div>
          </a>
        `;
        list.appendChild(li);
      });
    } else {
      addMissing.style.display = "flex";
    }
  }

  function fetchResults(q) {
    const search = q.trim();

    clearTimeout(timer);
    timer = setTimeout(() => {
      fetch(
        `${courseSearchData.ajaxUrl}?action=reviewmvp_course_search&nonce=${
          courseSearchData.nonce
        }&q=${encodeURIComponent(search)}`
      )
        .then((res) => res.json())
        .then((data) => {
          if (data.length) {
            render(data);
          } else {
            render([]); // show Add Missing button
          }
        })
        .catch(() => render([]));
    }, 300);
  }

  function select(i) {
    const choice = currentResults[i];
    if (!choice) return;
    input.value = choice.name;
    close();
    if (choice.url) window.location.href = choice.url;
  }

  function move(delta) {
    if (!currentResults.length) return;
    activeIndex =
      (activeIndex + delta + currentResults.length) % currentResults.length;
    [...list.children].forEach((el, idx) => {
      el.setAttribute("aria-selected", String(idx === activeIndex));
      if (idx === activeIndex) {
        input.setAttribute("aria-activedescendant", el.id);
        el.scrollIntoView({ block: "nearest" });
      }
    });
  }

  // events
  input.addEventListener("focus", () => {
    open();
    fetchResults(input.value); // will fetch empty q → latest 10
  });
  input.addEventListener("input", () => {
    open();
    fetchResults(input.value);
  });

  input.addEventListener("keydown", (e) => {
    if (e.key === "ArrowDown") {
      e.preventDefault();
      move(1);
    } else if (e.key === "ArrowUp") {
      e.preventDefault();
      move(-1);
    } else if (e.key === "Enter") {
      if (activeIndex >= 0 && currentResults[activeIndex]) {
        e.preventDefault();
        select(activeIndex);
      } else if (!currentResults.length) {
        e.preventDefault();

        // Save term in session via AJAX, then redirect
        fetch(courseSearchData.ajaxUrl, {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: new URLSearchParams({
            action: "reviewmvp_save_no_match_term",
            nonce: courseSearchData.nonce,
            term: input.value.trim(),
          }),
        }).then(() => {
          window.location.href = `${window.location.origin}/no-course-found/`;
        });
      }
    } else if (e.key === "Escape") {
      close();
    }
  });

  addMissing.addEventListener("click", () => {
    window.location.href = addMissingUrl;
  });

  document.addEventListener("click", (e) => {
    if (!wrap.contains(e.target)) close();
  });

  close();
})();
